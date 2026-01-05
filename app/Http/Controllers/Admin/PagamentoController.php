<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PagamentoController extends Controller
{
    /**
     * Exibe a lista de pagamentos (Contas a Receber), permitindo filtros.
     */
    public function index(Request $request)
    {
        // 1. Query base com relacionamentos necessários
        $baseQuery = Pagamento::with(['aluno.turma', 'registradoPor'])
            ->orderBy('data_vencimento', 'asc');

        // Status solicitado (padrão: Pendente)
        $status = $request->get('status', 'Pendente');

        // Data de hoje à meia-noite (para cálculos de atraso)
        $hoje = now()->startOfDay();

        // 2. Construir a query filtrada (para listagem e paginação)
        $query = clone $baseQuery;

        if ($status === 'Atrasado') {
            $query->where('status', 'Pendente')
                ->where('data_vencimento', '<', $hoje);
        } elseif ($status === 'Pendente') {
            $query->where('status', 'Pendente')
                ->where('data_vencimento', '>=', $hoje);
        } else {
            $query->where('status', $status);
        }

        // Filtro por nome do aluno
        if ($request->filled('aluno')) {
            $query->whereHas('aluno', function ($q) use ($request) {
                $q->where('nome_completo', 'like', '%' . $request->aluno . '%');
            });
        }

        // Paginação (apenas os registros da página atual)
        $pagamentos = $query->paginate(20)->appends($request->query());

        // 3. CÁLCULOS DOS TOTAIS CORRETOS (sobre TODOS os registros filtrados, não só a página)
        $totaisQuery = clone $baseQuery;

        // Aplicar os mesmos filtros de status e aluno usados na listagem
        if ($status === 'Atrasado') {
            $totaisQuery->where('status', 'Pendente')
                ->where('data_vencimento', '<', $hoje);
        } elseif ($status === 'Pendente') {
            $totaisQuery->where('status', 'Pendente')
                ->where('data_vencimento', '>=', $hoje);
        } else {
            $totaisQuery->where('status', $status);
        }

        if ($request->filled('aluno')) {
            $totaisQuery->whereHas('aluno', function ($q) use ($request) {
                $q->where('nome_completo', 'like', '%' . $request->aluno . '%');
            });
        }

        // Totais corretos
        $totalPrevisto  = $totaisQuery->sum('valor_previsto');
        $totalParcelas  = $totaisQuery->count();

        // Valor total em atraso (independente do filtro atual de status)
        // Calculamos separadamente, pois "Atrasado" é um status virtual
        $totalAtrasado = (clone $baseQuery)
            ->where('status', 'Pendente')
            ->where('data_vencimento', '<', $hoje)
            ->when($request->filled('aluno'), function ($q) use ($request) {
                return $q->whereHas('aluno', function ($sub) use ($request) {
                    $sub->where('nome_completo', 'like', '%' . $request->aluno . '%');
                });
            })
            ->sum('valor_previsto');

        // Opções de filtro
        $status_options = ['Pendente', 'Pago', 'Atrasado', 'Cancelado'];

        // Passar tudo para a view
        return view('admin.pagamentos.index', compact(
            'pagamentos',
            'status',
            'status_options',
            'totalPrevisto',
            'totalAtrasado',
            'totalParcelas'
        ));
    }

    /**
     * Mostra o formulário para dar baixa no pagamento.
     * Na prática, usaremos o método 'update' para processar a baixa.
     */
    public function show(Pagamento $pagamento)
    {
        return view('admin.pagamentos.show', compact('pagamento'));
    }

    /**
     * Processa a baixa/atualização de status de uma parcela.
     */
    public function update(Request $request, Pagamento $pagamento)
    {
        $data = $request->validate([
            'action' => ['required', 'string', 'in:pay,cancel,reopen'], // Ação a ser executada
            'valor_pago' => ['nullable', 'numeric', 'min:0'],
            'data_pagamento' => ['nullable', 'date'],
            'metodo_pagamento' => ['nullable', 'string', 'max:50'],
            'observacoes' => ['nullable', 'string', 'max:500'],
        ]);

        $pagamento->registrado_por_user_id = Auth::id(); // Registra quem está atualizando

        if ($data['action'] === 'pay') {
            // Processa o pagamento
            $pagamento->status = 'Pago';
            $pagamento->data_pagamento = $data['data_pagamento'] ?? Carbon::now();
            $pagamento->valor_pago = $data['valor_pago'] ?? $pagamento->valor_previsto;
            $pagamento->metodo_pagamento = $data['metodo_pagamento'];
            $pagamento->observacoes = $data['observacoes'];

            $pagamento->save();
            $message = 'Pagamento da parcela ' . $pagamento->parcela_numero . ' do aluno ' . $pagamento->aluno->nome_completo . ' registrado como PAGO.';
        } elseif ($data['action'] === 'cancel') {
            // Cancela a parcela
            $pagamento->status = 'Cancelado';
            $pagamento->data_pagamento = null;
            $pagamento->valor_pago = null;
            $pagamento->metodo_pagamento = null;
            $pagamento->observacoes = $data['observacoes'];

            $pagamento->save();
            $message = 'Parcela ' . $pagamento->parcela_numero . ' cancelada.';
        } elseif ($data['action'] === 'reopen') {
            // Reabre a parcela (volta para Pendente)
            $pagamento->status = 'Pendente';
            $pagamento->data_pagamento = null;
            $pagamento->valor_pago = null;
            $pagamento->metodo_pagamento = null;
            $pagamento->observacoes = null;

            $pagamento->save();
            $message = 'Parcela ' . $pagamento->parcela_numero . ' reaberta (Pendente).';
        }

        return redirect()->route('admin.pagamentos.index', ['status' => $pagamento->status])->with('success', $message);
    }
}
