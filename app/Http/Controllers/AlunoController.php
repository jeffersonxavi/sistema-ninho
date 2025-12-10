<?php

namespace App\Http\Controllers;

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
        $alunos = Aluno::with('turma', 'pagamentos') // Garante que Pagamentos seja carregado
            ->get();

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
            // 1. CRIAÇÃO DO ALUNO
            $aluno = Aluno::create([
                // Dados Pessoais
                'nome_completo' => $data['nome_completo'],
                'data_nascimento' => $data['data_nascimento'],
                'nome_responsavel' => $data['nome_responsavel'],
                'rg' => $data['rg'] ?? null,
                'cpf' => $data['cpf'] ?? null,
                'endereco' => $data['endereco'] ?? null,
                'telefone' => $data['telefone'],

                // Dados da Matrícula/Curso
                'turma_id' => $data['turma_id'],
                'data_matricula' => $data['data_matricula'],
                'termino_contrato' => $data['termino_contrato'] ?? null,
                'periodo' => $data['periodo'],
                'horario' => $data['horario'] ?? null,
                'dias_da_semana' => $data['dias_da_semana'] ?? null,

                // Dados Financeiros
                'valor_total' => $data['valor_total'],
                'valor_parcela' => $data['valor_parcela'],
                'qtd_parcelas' => $data['qtd_parcelas'],
                'forma_pagamento' => $data['forma_pagamento'],

                // Status e Auditoria
                'contrato_gerado' => false,
                'cadastrado_por_user_id' => Auth::id(),
            ]);

            // 2. GERAÇÃO AUTOMÁTICA DE PARCELAS
            $qtd_parcelas = $data['qtd_parcelas'];
            $valor_parcela = $data['valor_parcela'];
            $dia_vencimento = intval($data['dia_vencimento']);

            $data_matricula = Carbon::parse($data['data_matricula']);

            // Determina a data de vencimento da primeira parcela: mês seguinte, no dia escolhido.
            $vencimento = $data_matricula->copy()->addMonth();

            // Se o dia escolhido for maior que o número de dias do próximo mês, Carbon ajusta
            // para o último dia válido, mas addMonthNoOverflow() previne isso e garante o dia 1, 5 ou 10.
            // É importante setar o dia no final para evitar problemas se a matricula for dia 31 e o mês seguinte não tiver.
            $vencimento->day($dia_vencimento);

            $parcelas = [];

            for ($i = 1; $i <= $qtd_parcelas; $i++) {
                // Garante que a data de vencimento está ajustada para o formato Y-m-d
                $data_vencimento_parcela = $vencimento->format('Y-m-d');

                $parcelas[] = [
                    'aluno_id' => $aluno->id,
                    'parcela_numero' => $i,
                    'valor_previsto' => $valor_parcela,
                    'data_vencimento' => $data_vencimento_parcela,
                    'status' => 'Pendente',
                    'registrado_por_user_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Avança para o próximo mês, mantendo o dia 1, 5 ou 10
                $vencimento->addMonthNoOverflow();
            }

            // Insere todas as parcelas de uma vez para melhor performance
            Pagamento::insert($parcelas);

            // 3. Finalização e Marcação do Contrato
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
        $turmas = Turma::with('sala')->orderBy('nome')->get();
        $dias_vencimento = [1, 5, 10];

        return view('admin.alunos.edit', compact('aluno', 'turmas', 'dias_vencimento'));
    }

    public function update(Request $request, Aluno $aluno)
    {
        $data = $request->validate([
            // Validações iguais às do Store (o campo 'dia_vencimento' será ignorado)
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

        // Remove campos que não estão no fillable ou que são apenas auxiliares
        unset($data['dia_vencimento']);

        $aluno->update($data);

        return redirect()->route('admin.alunos.index')->with('success', 'Aluno atualizado com sucesso!');
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

        $pendentes = $pagamentos->where('status', 'Pendente')->count();
        $atrasados = $pagamentos->where('status', 'Atrasado')->count();
        $pagos = $pagamentos->where('status', 'Pago')->count();

        // Regra para definir o status principal
        if ($atrasados > 0) {
            return "Em Dívida ({$atrasados} Atrasada(s))";
        } elseif ($pendentes == 0 && $pagos > 0) {
            return 'Contrato Finalizado/Pago';
        } elseif ($pendentes > 0 && $pagos == 0) {
            return 'Em Andamento (1ª pendente)';
        }

        return 'Em Andamento';
    }

    // ... (fim da classe) ...
}
