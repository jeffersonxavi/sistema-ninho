<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PagamentoController extends Controller
{
public function index(Request $request)
{
    $status = $request->get('status', 'Pendente');
    $hoje = now()->startOfDay();

    $query = Pagamento::with(['aluno.turma', 'registradoPor'])
        ->orderBy('data_vencimento', 'asc');

    // Nova lógica de filtros
    if ($status === 'Atrasado') {
        $query->where('status', 'Pendente')->where('data_vencimento', '<', $hoje);
    } elseif ($status === 'Pendente') {
        $query->where('status', 'Pendente')->where('data_vencimento', '>=', $hoje);
    } elseif ($status === 'Todos') {
        // Não aplica filtro de status nem de data, traz tudo do banco
    } else {
        $query->where('status', $status);
    }

    if ($request->filled('aluno')) {
        $query->whereHas('aluno', fn($q) => $q->where('nome_completo', 'like', "%{$request->aluno}%"));
    }

    $pagamentos = $query->paginate(20)->withQueryString();
    
    // Adicione 'Todos' ao array de opções
    $status_options = ['Pendente', 'Atrasado', 'Pago', 'Cancelado', 'Todos'];

    return view('staff.pagamentos.index', compact('pagamentos', 'status', 'status_options'));
}

    public function show(Pagamento $pagamento)
    {
        // Ajustado para a view de staff
        return view('staff.pagamentos.show', compact('pagamento'));
    }

   public function update(Request $request, Pagamento $pagamento)
{
    $data = $request->validate([
        'action' => ['required', 'string', 'in:pay,cancel,reopen'], 
        'observacoes' => ['nullable', 'string', 'max:500'],
    ]);

    $pagamento->registrado_por_user_id = Auth::id();

    if ($data['action'] === 'pay') {
        $pagamento->status = 'Pago';
        $pagamento->data_pagamento = now(); // Data de hoje
        $pagamento->valor_pago = $pagamento->valor_previsto; // Copia o valor interno do banco
        $pagamento->metodo_pagamento = 'Dinheiro/Manual'; // Define um padrão
        $pagamento->observacoes = $data['observacoes'];
        $message = 'Baixa realizada com sucesso!';
    } 
    // ... manter lógica de cancel e reopen
    
    $pagamento->save();

    return redirect()->route('staff.pagamentos.index', ['status' => 'Pago'])->with('success', $message);
}
}