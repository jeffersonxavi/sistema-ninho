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
        // 1. Inicia a query com os vínculos necessários
        $query = Pagamento::with(['aluno.turma', 'registradoPor'])
            ->orderBy('data_vencimento', 'asc');

        // Define o status atual da requisição. Padrão: Pendente
        $status = $request->get('status', 'Pendente');

        // Calcula a data de hoje à meia-noite para comparações
        $hoje = now()->startOfDay();

        // 2. Aplica o filtro de status de forma condicional
        if ($status === 'Atrasado') {
            // Se for "Atrasado", buscamos no DB:
            // A) O status *real* é 'Pendente'
            // B) A data de vencimento é *anterior* a hoje
            $query->where('status', 'Pendente')
                ->where('data_vencimento', '<', $hoje);
        } elseif ($status === 'Pendente') {
            // Se for "Pendente", buscamos no DB:
            // A) O status *real* é 'Pendente'
            // B) A data de vencimento é *maior ou igual* a hoje (ou seja, ainda não venceu)
            $query->where('status', 'Pendente')
                ->where('data_vencimento', '>=', $hoje);
        } else {
            // Para 'Pago', 'Cancelado' e outros status fixos
            $query->where('status', $status);
        }

        // Exemplo: filtro por nome do aluno
        if ($request->filled('aluno')) {
            $query->whereHas('aluno', function ($q) use ($request) {
                $q->where('nome_completo', 'like', '%' . $request->aluno . '%');
            });
        }

        $pagamentos = $query->paginate(20);

        // Lista completa de opções para a view
        $status_options = ['Pendente', 'Pago', 'Atrasado', 'Cancelado'];

        return view('admin.pagamentos.index', compact('pagamentos', 'status', 'status_options'));
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
