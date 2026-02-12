<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Pagamento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlunoController extends Controller
{
    public function index()
    {
        $alunos = Aluno::with([
            'turma.sala',
            'pagamentos',
            'responsavel'
        ])->get();

        $alunos = $alunos->map(function ($aluno) {
            $dataAtual = now();

            // Pagamentos que ainda não venceram e não foram pagos
            $pagamentosPendentes = $aluno->pagamentos
                ->where('data_vencimento', '>', $dataAtual)
                ->where('status', 'Pendente');

            // Pagamentos que venceram e não foram pagos
            $pagamentosAtrasados = $aluno->pagamentos
                ->where('data_vencimento', '<=', $dataAtual)
                ->where('status', 'Pendente'); // Assumindo que 'Pendente' é o status inicial

            // 1. Prioridade máxima: Atrasado (dívida)
            if ($pagamentosAtrasados->isNotEmpty()) {
                $aluno->status_financeiro_geral = 'Atrasado';
            }
            // 2. Se não há atraso, mas há parcelas a vencer
            else if ($pagamentosPendentes->isNotEmpty()) {
                $aluno->status_financeiro_geral = 'Em Curso (Pendente)';
            }
            // 3. Se não há parcelas a vencer, nem atraso, e o total de parcelas pagas é igual ao total de parcelas
            else if ($aluno->pagamentos->where('status', 'Pago')->count() == $aluno->qtd_parcelas && $aluno->qtd_parcelas > 0) {
                $aluno->status_financeiro_geral = 'Quitado (Pago)';
            }
            // 4. Default: Alguma inconsistência ou recém-cadastrado
            else {
                $aluno->status_financeiro_geral = 'Aguardando Pagamento';
            }

            return $aluno;
        });

        return view('admin.alunos.index', compact('alunos'));
    }


    public function create()
    {
        $turmas = Turma::with('sala')->orderBy('nome')->get();
        $dias_vencimento = [1, 5, 10];

        return view('admin.alunos.create', compact('turmas', 'dias_vencimento'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            // Validações ajustadas
            'nome_completo' => ['required', 'string', 'max:255'],
            'data_nascimento' => ['required', 'date'],
            'nome_responsavel' => ['required', 'string', 'max:255'],
            'rg' => ['nullable', 'string', 'max:30'],
            'cpf' => ['nullable', 'string', 'max:14'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],

            // Dados do Curso/Turma
            'turma_id' => ['required', 'exists:turmas,id'],
            'data_matricula' => ['required', 'date'],
            'termino_contrato' => ['nullable', 'date', 'after:data_matricula'],
            'periodo' => ['required', 'string', 'in:Manhã,Tarde,Noite,Integral'],
            'horario' => ['nullable', 'date_format:H:i'],
            'dias_da_semana' => ['nullable', 'string', 'max:255'],

            // Dados Financeiros
            'valor_total' => ['required', 'numeric', 'min:0'],
            'valor_parcela' => ['required', 'numeric', 'min:0.01'],
            'qtd_parcelas' => ['required', 'integer', 'min:1', 'max:60'],
            'forma_pagamento' => ['required', 'string', 'max:50'],
            'dia_vencimento' => ['required', 'integer', 'in:1,5,10'], // Dias válidos
        ]);

        DB::beginTransaction();

        try {

            // 1️⃣ CRIA OU BUSCA O RESPONSÁVEL
            $responsavel = \App\Models\Responsavel::firstOrCreate(
                ['cpf' => $data['cpf']],
                [
                    'nome' => $data['nome_responsavel'],
                    'rg' => $data['rg'] ?? null,
                    'telefone' => $data['telefone'],
                    'endereco' => $data['endereco'] ?? null,
                ]
            );

            // 2️⃣ CRIAÇÃO DO ALUNO (SEM CPF E RG)
            $aluno = Aluno::create([
                'nome_completo' => $data['nome_completo'],
                'data_nascimento' => $data['data_nascimento'],

                // AGORA USA A FK
                'responsavel_id' => $responsavel->id,

                // Dados da Matrícula
                'turma_id' => $data['turma_id'],
                'data_matricula' => $data['data_matricula'],
                'termino_contrato' => $data['termino_contrato'] ?? null,
                'periodo' => $data['periodo'],
                'horario' => $data['horario'] ?? null,
                'dias_da_semana' => $data['dias_da_semana'] ?? null,

                // Financeiro
                'valor_total' => $data['valor_total'],
                'valor_parcela' => $data['valor_parcela'],
                'qtd_parcelas' => $data['qtd_parcelas'],
                'forma_pagamento' => $data['forma_pagamento'],

                'contrato_gerado' => false,
                'cadastrado_por_user_id' => Auth::id(),
            ]);

            // 2. GERAÇÃO AUTOMÁTICA DE PARCELAS
            $qtd_parcelas = $data['qtd_parcelas'];
            $valor_parcela = $data['valor_parcela'];
            $dia_vencimento = intval($data['dia_vencimento']);

            $data_matricula = Carbon::parse($data['data_matricula']);

            $parcelas = [];
            /*
            |--------------------------------------------------------------------------
            | 1ª PARCELA = DATA DA MATRÍCULA
            |--------------------------------------------------------------------------
            */
            $parcelas[] = [
                'aluno_id' => $aluno->id,
                'parcela_numero' => 1,
                'valor_previsto' => $valor_parcela,
                'data_vencimento' => $data_matricula->format('Y-m-d'),
                'status' => 'Pendente',
                'registrado_por_user_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            /*
            |--------------------------------------------------------------------------
            | RESTANTES = DIA FIXO (1, 5 ou 10)
            |--------------------------------------------------------------------------
            */

            $vencimento = $data_matricula->copy()->addMonthNoOverflow();
            $vencimento->day($dia_vencimento);

            for ($i = 2; $i <= $qtd_parcelas; $i++) {

                $parcelas[] = [
                    'aluno_id' => $aluno->id,
                    'parcela_numero' => $i,
                    'valor_previsto' => $valor_parcela,
                    'data_vencimento' => $vencimento->format('Y-m-d'),
                    'status' => 'Pendente',
                    'registrado_por_user_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $vencimento->addMonthNoOverflow();
            }

            Pagamento::insert($parcelas);

            $aluno->update(['contrato_gerado' => true]);

            DB::commit();

            // 4. REDIRECIONAMENTO PARA O DOWNLOAD DO CONTRATO PDF (Ajuste principal)
            return redirect()->route('admin.alunos.show', $aluno)
                ->with('success', 'Aluno cadastrado e parcelas geradas com sucesso! O download do contrato será iniciado automaticamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log do erro para depuração
            // \Log::error('Erro ao cadastrar aluno e gerar parcelas: ' . $e->getMessage()); 
            return back()->withInput()->withErrors(['erro' => 'Falha ao cadastrar o aluno e gerar parcelas. Por favor, tente novamente. Detalhes: ' . $e->getMessage()]);
        }
    }
    /**
     * Gera e baixa o Contrato PDF.
     */
    public function downloadContrato(Aluno $aluno)
    {
        // Verifica se o aluno tem a turma relacionada (necessário para o contrato)
        if (!$aluno->turma) {
            return redirect()->back()->with('error', 'Não foi possível gerar o contrato: O aluno não está vinculado a uma turma.');
        }

        // Carrega a View Blade do contrato, passando o objeto $aluno
        $pdf = Pdf::loadView('admin.alunos.contrato', compact('aluno'));

        // Retorna o PDF para download
        $nomeContrato = 'Contrato_' . $aluno->nome_completo . '_' . date('Ymd') . '.pdf';

        //return $pdf->download($nomeContrato);
        // Ou, se preferir apenas visualizar no navegador:
        return $pdf->stream($nomeContrato);
    }
    public function edit(Aluno $aluno)
    {
        $aluno->load('responsavel');
        $turmas = Turma::with('sala')->orderBy('nome')->get();
        $dias_vencimento = [1, 5, 10];

        return view('admin.alunos.edit', compact('aluno', 'turmas', 'dias_vencimento'));
    }

    public function update(Request $request, Aluno $aluno)
    {
        $data = $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'data_nascimento' => ['required', 'date'],
            'nome_responsavel' => ['required', 'string', 'max:255'],
            'rg' => ['nullable', 'string', 'max:30'],
            'cpf' => ['nullable', 'string', 'max:14'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'turma_id' => ['required', 'exists:turmas,id'],
            'data_matricula' => ['required', 'date'],
            'termino_contrato' => ['nullable', 'date', 'after:data_matricula'],
            'periodo' => ['required', 'string', 'in:Manhã,Tarde,Noite,Integral'],
            'horario' => ['nullable', 'date_format:H:i'],
            'dias_da_semana' => ['nullable', 'string', 'max:255'],
            'valor_total' => ['required', 'numeric', 'min:0'],
            'valor_parcela' => ['required', 'numeric', 'min:0.01'],
            'qtd_parcelas' => ['required', 'integer', 'min:1', 'max:60'],
            'forma_pagamento' => ['required', 'string', 'max:50'],
        ]);

        DB::beginTransaction();

        try {

            // 🔹 Atualiza ou cria responsável se não existir
            if ($aluno->responsavel) {
                $aluno->responsavel->update([
                    'nome' => $data['nome_responsavel'],
                    'cpf' => $data['cpf'] ?? null,
                    'rg' => $data['rg'] ?? null,
                    'telefone' => $data['telefone'],
                    'endereco' => $data['endereco'] ?? null,
                ]);
            } else {
                $responsavel = \App\Models\Responsavel::create([
                    'nome' => $data['nome_responsavel'],
                    'cpf' => $data['cpf'] ?? null,
                    'rg' => $data['rg'] ?? null,
                    'telefone' => $data['telefone'],
                    'endereco' => $data['endereco'] ?? null,
                ]);

                $aluno->responsavel_id = $responsavel->id;
                $aluno->save();
            }

            // 🔹 Atualiza apenas dados do aluno
            $aluno->update([
                'nome_completo' => $data['nome_completo'],
                'data_nascimento' => $data['data_nascimento'],
                'turma_id' => $data['turma_id'],
                'data_matricula' => $data['data_matricula'],
                'termino_contrato' => $data['termino_contrato'] ?? null,
                'periodo' => $data['periodo'],
                'horario' => $data['horario'] ?? null,
                'dias_da_semana' => $data['dias_da_semana'] ?? null,
                'valor_total' => $data['valor_total'],
                'valor_parcela' => $data['valor_parcela'],
                'qtd_parcelas' => $data['qtd_parcelas'],
                'forma_pagamento' => $data['forma_pagamento'],
            ]);

            DB::commit();

            return redirect()->route('admin.alunos.index')
                ->with('success', 'Aluno atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['erro' => 'Erro ao atualizar aluno: ' . $e->getMessage()]);
        }
    }


    public function destroy(Aluno $aluno)
    {
        $aluno->delete();
        return redirect()->route('admin.alunos.index')->with('success', 'Aluno excluído e todos os seus registros de pagamento removidos.');
    }

    // ... (dentro da class AlunoController extends Controller) ...

    /**
     * Exibe os detalhes de um aluno e suas parcelas de pagamento.
     */
    public function show(Aluno $aluno)
    {
        // Carrega as parcelas (Pagamentos) ordenadas por data de vencimento.
        // Carrega também a turma e o usuário que cadastrou.
        $aluno->load([
            'turma.sala',
            'cadastradoPor',
            'pagamentos' => function ($query) {
                $query->orderBy('data_vencimento', 'asc');
            }
        ]);

        // Determinar o status geral
        $status_geral = $this->calcularStatusGeral($aluno);

        return view('admin.alunos.show', compact('aluno', 'status_geral'));
    }

    /**
     * Calcula um status financeiro geral do aluno baseado nas parcelas.
     */
    private function calcularStatusGeral(Aluno $aluno): string
    {
        $pagamentos = $aluno->pagamentos;

        if ($pagamentos->isEmpty()) {
            return 'Sem Parcelas Geradas';
        }

        $hoje = now();

        $atrasados = $pagamentos->filter(function ($p) use ($hoje) {
            return $p->status === 'Pendente'
                && Carbon::parse($p->data_vencimento)->lt($hoje);
        })->count();

        $pendentes = $pagamentos->filter(function ($p) use ($hoje) {
            return $p->status === 'Pendente'
                && Carbon::parse($p->data_vencimento)->gte($hoje);
        })->count();

        $pagos = $pagamentos->where('status', 'Pago')->count();

        if ($atrasados > 0) {
            return "Atrasado ({$atrasados})";
        }

        if ($pendentes > 0) {
            return 'Em Curso (Pendente)';
        }

        if ($pagos === $pagamentos->count()) {
            return 'Quitado (Pago)';
        }

        return 'Aguardando Pagamento';
    }


    // ... (fim da classe) ...
}
