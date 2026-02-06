@extends('layouts.app')

@section('title', 'Gerenciar Pagamento')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- CABEÇALHO --}}
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-indigo-700">
                                Parcela #{{ $pagamento->parcela_numero }}
                            </h3>
                            <p class="text-gray-600">Aluno: <span class="font-semibold">{{ $pagamento->aluno->nome_completo ?? 'N/A' }}</span></p>
                        </div>
                        <a href="{{ route('staff.pagamentos.index') }}" class="inline-flex items-center text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-md transition">
                            <i class="fas fa-arrow-left mr-2"></i> Voltar
                        </a>
                    </div>

                    {{-- ALERTAS DE ATRASO --}}
                    @php
                        $vencimento = \Carbon\Carbon::parse($pagamento->data_vencimento);
                        $atrasado = $pagamento->status === 'Pendente' && $vencimento->isPast() && !$vencimento->isToday();
                        $diasAtraso = $vencimento->diffInDays(now());
                    @endphp

                    @if($atrasado)
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-red-400"></i></div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700 font-bold">
                                        ESTA PARCELA ESTÁ ATRASADA HÁ {{ $diasAtraso }} DIA(S).
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- GRID DE INFORMAÇÕES --}}
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mb-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Valor Previsto</p>
                            <p class="text-xl font-black text-indigo-600">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Vencimento</p>
                            <p class="text-lg font-bold {{ $atrasado ? 'text-red-600' : '' }}">{{ $vencimento->format('d/m/Y') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Status Atual</p>
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                @if($pagamento->status == 'Pago') bg-green-100 text-green-800
                                @elseif($atrasado) bg-red-100 text-red-800
                                @elseif($pagamento->status == 'Pendente') bg-yellow-100 text-yellow-800
                                @else bg-gray-200 text-gray-700 @endif">
                                {{ $atrasado ? 'ATRASADO' : $pagamento->status }}
                            </span>
                        </div>
                    </div>

                    {{-- SEÇÃO DE AÇÃO --}}
                    @if ($pagamento->status == 'Pendente')
                        <div class="bg-white border-2 border-green-100 rounded-xl p-6 shadow-sm">
                            <h4 class="text-lg font-bold mb-4 text-green-700 flex items-center">
                                <i class="fas fa-cash-register mr-2"></i> Confirmar Recebimento
                            </h4>
                            
                            <form method="POST" action="{{ route('staff.pagamentos.update', $pagamento->id) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="action" value="pay">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700">Valor Recebido (R$)</label>
                                        <input type="number" step="0.01" name="valor_pago" value="{{ old('valor_pago', $pagamento->valor_previsto) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700">Data da Baixa</label>
                                        <input type="date" name="data_pagamento" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700">Forma de Pagamento</label>
                                        <select name="metodo_pagamento" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                                            <option value="">Selecione...</option>
                                            <option value="PIX">PIX</option>
                                            <option value="Dinheiro">Dinheiro</option>
                                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                                            <option value="Cartão de Débito">Cartão de Débito</option>
                                            <option value="Transferência">Transferência / TED</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700">Observações Internas</label>
                                    <textarea name="observacoes" rows="2" placeholder="Algum detalhe importante?" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-3 rounded-lg shadow-md transition">
                                    BAIXAR PAGAMENTO AGORA
                                </button>
                            </form>
                        </div>

                        {{-- BOTÃO CANCELAR DISCRETO --}}
                        <div class="mt-8 pt-6 border-t flex justify-end">
                            <form action="{{ route('admin.pagamentos.update', $pagamento->id) }}" method="POST" onsubmit="return confirm('Confirmar o cancelamento desta parcela?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit" class="text-gray-400 hover:text-red-600 text-sm flex items-center transition">
                                    <i class="fas fa-times-circle mr-1"></i> Cancelar esta parcela
                                </button>
                            </form>
                        </div>

                    @else
                        {{-- HISTÓRICO DO PAGAMENTO --}}
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                            <h4 class="text-lg font-bold mb-4 text-blue-800">Detalhes do Recebimento</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between border-b border-blue-100 pb-2">
                                    <span class="text-gray-600 font-medium">Data do Pagamento:</span>
                                    <span class="font-bold text-gray-800">{{ $pagamento->data_pagamento ? \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y') : '-' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-blue-100 pb-2">
                                    <span class="text-gray-600 font-medium">Valor Pago:</span>
                                    <span class="font-bold text-green-700 text-lg">R$ {{ number_format($pagamento->valor_pago, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between border-b border-blue-100 pb-2">
                                    <span class="text-gray-600 font-medium">Método:</span>
                                    <span class="font-bold text-gray-800">{{ $pagamento->metodo_pagamento }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 font-medium">Operador:</span>
                                    <span class="font-bold text-gray-800">{{ $pagamento->registradoPor->name ?? 'Sistema' }}</span>
                                </div>
                            </div>

                            @if($pagamento->status == 'Pago')
                                <form action="{{ route('staff.pagamentos.update', $pagamento->id) }}" method="POST" class="mt-6 pt-4 border-t border-blue-200" onsubmit="return confirm('Atenção: A parcela voltará para pendente. Continuar?')">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="reopen">
                                    <button type="submit" class="text-yellow-700 hover:text-yellow-800 font-bold text-xs uppercase tracking-tighter">
                                        <i class="fas fa-undo mr-1"></i> Erro no registro? Reabrir Parcela
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection