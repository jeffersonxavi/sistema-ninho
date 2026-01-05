<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Turma;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlunoController extends Controller
{
    private function autorizar(Aluno $aluno)
    {
        if ($aluno->cadastrado_por_user_id !== Auth::id()) {
            abort(403, 'Acesso restrito ao criador deste aluno.');
        }
    }

    public function index()
    {
        // Staff só vê alunos criados por ele
        $alunos = Auth::user()
            ->alunosCadastrados()
            ->with('turma', 'pagamentos')
            ->get();

        return view('staff.alunos.index', compact('alunos'));
    }

    public function show(Aluno $aluno)
    {
        $this->autorizar($aluno);

        $aluno->load([
            'turma.sala',
            'cadastradoPor',
            'pagamentos' => function ($query) {
                $query->orderBy('data_vencimento', 'asc');
            }
        ]);

        $status_geral = $this->calcularStatusGeral($aluno);

        return view('staff.alunos.show', compact('aluno', 'status_geral'));
    }

    private function calcularStatusGeral(Aluno $aluno): string
    {
        $pagamentos = $aluno->pagamentos;

        if ($pagamentos->isEmpty()) {
            return 'Sem Parcelas Geradas';
        }

        $pendentes = $pagamentos->where('status', 'Pendente')->count();
        $atrasados = $pagamentos
            ->where('data_vencimento', '<', now())
            ->where('status', 'Pendente')
            ->count();
        $pagos = $pagamentos->where('status', 'Pago')->count();

        if ($atrasados > 0) {
            return "Em Dívida ({$atrasados} Atrasada(s))";
        }

        if ($pendentes == 0 && $pagos > 0) {
            return 'Contrato Finalizado/Pago';
        }

        if ($pendentes > 0 && $pagos == 0) {
            return 'Em Andamento (1ª pendente)';
        }

        return 'Em Andamento';
    }

    public function create()
    {
        $turmas = Turma::with('sala')->orderBy('nome')->get();
        $dias_vencimento = [1, 5, 10];

        return view('staff.alunos.create', compact('turmas', 'dias_vencimento'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_completo'      => ['required', 'string', 'max:255'],
            'data_nascimento'    => ['required', 'date'],
            'nome_responsavel'   => ['required', 'string', 'max:255'],
            'rg'                 => ['nullable', 'string', 'max:30'],
            'cpf'                => ['nullable', 'string', 'max:14'],
            'endereco'           => ['nullable', 'string', 'max:255'],
            'telefone'           => ['required', 'string', 'max:20'],

            'turma_id'           => ['required', 'exists:turmas,id'],
            'data_matricula'     => ['required', 'date'],
            'termino_contrato'   => ['nullable', 'date'],
            'periodo'            => ['required', 'string'],
            'horario'            => ['nullable', 'date_format:H:i'],
            'dias_da_semana'     => ['nullable', 'string'],

            'valor_total'        => ['required', 'numeric', 'min:0'],
            'valor_parcela'      => ['required', 'numeric', 'min:0.01'],
            'qtd_parcelas'       => ['required', 'integer', 'min:1', 'max:60'],
            'forma_pagamento'    => ['required', 'string'],
            'dia_vencimento'     => ['required', 'integer', 'in:1,5,10'],
        ]);

        DB::beginTransaction();

        try {
            $aluno = Aluno::create([
                'nome_completo' => $data['nome_completo'],
                'data_nascimento' => $data['data_nascimento'],
                'nome_responsavel' => $data['nome_responsavel'],
                'rg' => $data['rg'],
                'cpf' => $data['cpf'],
                'endereco' => $data['endereco'],
                'telefone' => $data['telefone'],

                'turma_id' => $data['turma_id'],
                'data_matricula' => $data['data_matricula'],
                'termino_contrato' => $data['termino_contrato'],
                'periodo' => $data['periodo'],
                'horario' => $data['horario'],
                'dias_da_semana' => $data['dias_da_semana'],

                'valor_total' => $data['valor_total'],
                'valor_parcela' => $data['valor_parcela'],
                'qtd_parcelas' => $data['qtd_parcelas'],
                'forma_pagamento' => $data['forma_pagamento'],

                'contrato_gerado' => false,
                'cadastrado_por_user_id' => Auth::id(),
            ]);

            // GERAÇÃO DAS PARCELAS
            $qtd = $data['qtd_parcelas'];
            $valor = $data['valor_parcela'];
            $dia_venc = intval($data['dia_vencimento']);
            $data_matricula = Carbon::parse($data['data_matricula']);

            $vencimento = $data_matricula->copy()->addMonth()->day($dia_venc);

            $parcelas = [];

            for ($i = 1; $i <= $qtd; $i++) {
                $parcelas[] = [
                    'aluno_id' => $aluno->id,
                    'parcela_numero' => $i,
                    'valor_previsto' => $valor,
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

            return redirect()->route('staff.alunos.show', $aluno)
                ->with('success', 'Aluno cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['erro' => $e->getMessage()]);
        }
    }

    public function edit(Aluno $aluno)
    {
        $this->autorizar($aluno);

        $turmas = Turma::with('sala')->orderBy('nome')->get();
        $dias_vencimento = [1, 5, 10];

        return view('staff.alunos.edit', compact('aluno', 'turmas', 'dias_vencimento'));
    }

    public function update(Request $request, Aluno $aluno)
    {
        $this->autorizar($aluno);

        $data = $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'data_nascimento' => ['required', 'date'],
            'nome_responsavel' => ['required', 'string', 'max:255'],
            'rg' => ['nullable', 'string'],
            'cpf' => ['nullable', 'string'],
            'endereco' => ['nullable', 'string'],
            'telefone' => ['required', 'string'],
            'turma_id' => ['required', 'exists:turmas,id'],
            'data_matricula' => ['required', 'date'],
            'termino_contrato' => ['nullable', 'date'],
            'periodo' => ['required', 'string'],
            'horario' => ['nullable', 'date_format:H:i'],
            'dias_da_semana' => ['nullable', 'string'],
            'valor_total' => ['required', 'numeric'],
            'valor_parcela' => ['required', 'numeric'],
            'qtd_parcelas' => ['required', 'integer'],
            'forma_pagamento' => ['required', 'string'],
        ]);

        unset($data['dia_vencimento']);

        $aluno->update($data);

        return redirect()->route('staff.alunos.index')
            ->with('success', 'Aluno atualizado!');
    }

    public function destroy(Aluno $aluno)
    {
        $this->autorizar($aluno);

        $aluno->delete();

        return redirect()->route('staff.alunos.index')
            ->with('success', 'Aluno removido!');
    }

    public function downloadContrato(Aluno $aluno)
    {
        $this->autorizar($aluno);

        $pdf = \PDF::loadView('staff.alunos.contrato', compact('aluno'));
        return $pdf->stream('Contrato_' . $aluno->nome_completo . '.pdf');
    }
}
