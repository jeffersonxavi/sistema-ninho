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
                                    <p class="text-sm text-red-700 font-bold uppercase">
                                        Atenção: Parcela vencida há {{ $diasAtraso }} dia(s).
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- INFORMAÇÕES BÁSICAS --}}
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Vencimento Original</p>
                            <p class="text-lg font-bold {{ $atrasado ? 'text-red-600' : 'text-gray-800' }}">{{ $vencimento->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Status do Registro</p>
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                @if($pagamento->status == 'Pago') bg-green-100 text-green-800
                                @elseif($atrasado) bg-red-100 text-red-800
                                @elseif($pagamento->status == 'Pendente') bg-yellow-100 text-yellow-800
                                @else bg-gray-200 text-gray-700 @endif">
                                {{ $atrasado ? 'ATRASADO' : $pagamento->status }}
                            </span>
                        </div>
                    </div>

                    {{-- SEÇÃO DE BAIXA (APENAS SE ESTIVER PENDENTE) --}}
                    @if ($pagamento->status == 'Pendente')
                        <div class="bg-white border-2 border-indigo-50 rounded-xl p-6 shadow-sm">
                            <h4 class="text-lg font-bold mb-4 text-indigo-700 flex items-center">
                                <i class="fas fa-cash-register mr-2"></i> Registrar Recebimento
                            </h4>
                            
                            <form method="POST" action="{{ route('staff.pagamentos.update', $pagamento->id) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="action" value="pay">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700">Forma de Pagamento</label>
                                        <select name="metodo_pagamento" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value=""><Selecione class=""></Selecione></option>
                                            <option value="PIX">PIX</option>
                                            <option value="Dinheiro">Dinheiro</option>
                                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                                            <option value="Cartão de Débito">Cartão de Débito</option>
                                            <option value="Transferência">Transferência / TED</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700">Data do Recebimento</label>
                                        <input type="date" name="data_pagamento" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700">Observações (Opcional)</label>
                                    <textarea name="observacoes" rows="2" placeholder="Ex: Pagamento feito via chave aleatória." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-lg shadow-md transition flex justify-center items-center text-lg">
                                    <i class="fas fa-check-circle mr-2"></i> CONFIRMAR RECEBIMENTO
                                </button>
                            </form>
                        </div>
                    @else
                        {{-- HISTÓRICO APENAS PARA LEITURA --}}
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                            <h4 class="text-lg font-bold mb-4 text-blue-800">Dados do Pagamento Registrado</h4>
                            <div class="space-y-4">
                                <div class="flex justify-between border-b border-blue-100 pb-2">
                                    <span class="text-gray-600 font-medium">Data da Baixa:</span>
                                    <span class="font-bold text-gray-800">{{ $pagamento->data_pagamento ? \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y') : '-' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-blue-100 pb-2">
                                    <span class="text-gray-600 font-medium">Método Utilizado:</span>
                                    <span class="font-bold text-gray-800">{{ $pagamento->metodo_pagamento }}</span>
                                </div>
                                <div class="flex justify-between border-b border-blue-100 pb-2">
                                    <span class="text-gray-600 font-medium">Registrado por:</span>
                                    <span class="font-bold text-gray-800">{{ $pagamento->registradoPor->name ?? 'Sistema' }}</span>
                                </div>
                                
                                @if($pagamento->observacoes)
                                <div class="pt-2">
                                    <span class="text-xs text-gray-500 uppercase font-bold">Observações de baixa:</span>
                                    <p class="text-sm text-gray-700 mt-1 bg-white p-3 rounded-lg border border-blue-100 italic">
                                        "{{ $pagamento->observacoes }}"
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-6 text-center">
                            <p class="text-xs text-gray-400">
                                <i class="fas fa-lock mr-1"></i> Este registro foi finalizado e não pode ser alterado por este perfil.
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection