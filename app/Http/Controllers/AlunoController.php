<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlunoController extends Controller
{
    public function index()
    {
        $alunos = Aluno::with(['turma.sala', 'cadastradoPor'])->latest()->get();
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
            // Validações ajustadas para nullable conforme a Migration
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
            'dias_da_semana' => ['nullable', 'string', 'max:255'], // Nome Padronizado

            // Dados Financeiros
            'valor_total' => ['required', 'numeric', 'min:0'],
            'valor_parcela' => ['required', 'numeric', 'min:0.01'],
            'qtd_parcelas' => ['required', 'integer', 'min:1', 'max:60'], // Nome Padronizado
            'forma_pagamento' => ['required', 'string', 'max:50'],
            'dia_vencimento' => ['required', 'integer', 'in:1,5,10'],
        ]);

        DB::beginTransaction();

        try {
            // 1. CRIAÇÃO DO ALUNO (Sincronizado com os fillable do Model)
            $aluno = Aluno::create([
                'nome_completo' => $data['nome_completo'],
                'data_nascimento' => $data['data_nascimento'],
                'nome_responsavel' => $data['nome_responsavel'],
                'rg' => $data['rg'] ?? null,
                'cpf' => $data['cpf'] ?? null,
                'endereco' => $data['endereco'] ?? null,
                'telefone' => $data['telefone'],

                'turma_id' => $data['turma_id'],
                'data_matricula' => $data['data_matricula'],
                'termino_contrato' => $data['termino_contrato'] ?? null,
                'periodo' => $data['periodo'],
                'horario' => $data['horario'] ?? null,
                'dias_da_semana' => $data['dias_da_semana'] ?? null,

                'valor_total' => $data['valor_total'],
                'valor_parcela' => $data['valor_parcela'],
                'qtd_parcelas' => $data['qtd_parcelas'], // Usando 'qtd_parcelas'
                'forma_pagamento' => $data['forma_pagamento'],

                'contrato_gerado' => false,
                'cadastrado_por_user_id' => Auth::id(),
            ]);

            // 2. GERAÇÃO AUTOMÁTICA DE PARCELAS
            $qtd_parcelas = $data['qtd_parcelas'];
            $valor_parcela = $data['valor_parcela'];
            $dia_vencimento = $data['dia_vencimento'];

            $data_matricula = Carbon::parse($data['data_matricula']);
            $vencimento = $data_matricula->copy()->addMonth()->setDay(intval($dia_vencimento));

            $parcelas = [];

            for ($i = 1; $i <= $qtd_parcelas; $i++) {
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

            // 3. Finalização da Matrícula
            $aluno->update([
                'contrato_gerado' => true,
            ]);

            DB::commit();

            return redirect()->route('admin.alunos.index')->with('success', 'Aluno cadastrado e ' . $qtd_parcelas . ' parcelas geradas com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['erro' => 'Falha ao cadastrar o aluno e gerar parcelas. Erro: ' . $e->getMessage()]);
        }
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
