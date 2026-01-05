@extends('layouts.app')

@section('title', 'Gerenciar Pagamento')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gerenciar Parcela') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- TÍTULO E VOLTAR --}}
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <h3 class="text-2xl font-bold text-indigo-700">
                            Parcela #{{ $pagamento->parcela_numero }}
                            <span class="text-gray-500 font-normal text-lg"> (Aluno: {{ $pagamento->aluno->nome_completo ?? 'N/A' }})</span>
                        </h3>
                        <a href="{{ route('admin.pagamentos.index') }}" class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-3 rounded-md transition duration-150">
                            &larr; Voltar
                        </a>
                    </div>

                    {{-- BOX DE INFORMAÇÕES BÁSICAS --}}
                    <div class="border p-4 rounded-lg shadow-md mb-8">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Valor Previsto:</p>
                                <p class="font-bold text-lg text-indigo-800">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Vencimento:</p>
                                <p class="font-bold text-lg">{{ \Carbon\Carbon::parse($pagamento->data_vencimento)->format('d/m/Y') }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-gray-500">Status Atual:</p>
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    @if ($pagamento->status == 'Pago') bg-green-100 text-green-800
                                    @elseif ($pagamento->status == 'Pendente') bg-yellow-100 text-yellow-800
                                    @elseif ($pagamento->status == 'Atrasado') bg-red-100 text-red-800
                                    @elseif ($pagamento->status == 'Cancelado') bg-gray-200 text-gray-700
                                    @endif">
                                    {{ $pagamento->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Sucesso!</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Erro de Validação!</p>
                            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                        </div>
                    @endif

                    {{-- SEÇÃO DE REGISTRO / AÇÕES --}}
                    @if ($pagamento->status == 'Pendente' || $pagamento->status == 'Atrasado')
                        <h4 class="text-xl font-bold mb-4 text-green-700">Registrar Pagamento</h4>
                        
                        {{-- FORMULÁRIO PRINCIPAL DE PAGAMENTO (AÇÃO: PAY) --}}
                        <form method="POST" action="{{ route('admin.pagamentos.update', $pagamento->id) }}" class="p-4 border rounded-lg shadow-inner bg-green-50">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="pay">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="valor_pago" class="block text-sm font-medium text-gray-700">Valor Pago (R$)</label>
                                    <input type="number" step="0.01" name="valor_pago" id="valor_pago" value="{{ old('valor_pago', $pagamento->valor_previsto) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>
                                <div>
                                    <label for="data_pagamento" class="block text-sm font-medium text-gray-700">Data do Pagamento</label>
                                    <input type="date" name="data_pagamento" id="data_pagamento" value="{{ old('data_pagamento', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="metodo_pagamento" class="block text-sm font-medium text-gray-700">Método de Pagamento</label>
                                    <input type="text" name="metodo_pagamento" id="metodo_pagamento" value="{{ old('metodo_pagamento') }}" placeholder="Ex: PIX, Boleto Compensado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="observacoes" class="block text-sm font-medium text-gray-700">Observações (Opcional)</label>
                                    <textarea name="observacoes" id="observacoes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('observacoes') }}</textarea>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-lg transition duration-150">
                                CONFIRMAR RECEBIMENTO
                            </button>
                        </form>

                        {{-- FORMULÁRIO SEPARADO PARA CANCELAMENTO (AÇÃO: CANCEL) --}}
                        <h4 class="text-lg font-bold mt-8 mb-3 border-b pb-1 text-red-700">Outras Ações</h4>
                        <form action="{{ route('admin.pagamentos.update', $pagamento->id) }}" method="POST" class="inline" onsubmit="return confirm('ATENÇÃO! Tem certeza que deseja CANCELAR esta parcela? Isso afetará o financeiro.')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit" class="text-red-700 hover:text-white border border-red-500 hover:bg-red-600 py-2 px-4 rounded-md shadow-sm transition duration-150">
                                Cancelar Parcela
                            </button>
                        </form>
                        
                    @elseif ($pagamento->status == 'Pago' || $pagamento->status == 'Cancelado')
                        
                        {{-- SEÇÃO DE DETALHES DO REGISTRO --}}
                        <h4 class="text-xl font-bold mt-4 mb-4 text-gray-700">Detalhes do Registro</h4>
                        <div class="border p-4 rounded-lg shadow-sm bg-gray-50 text-sm space-y-2">
                            @if ($pagamento->data_pagamento)
                                <p><strong>Data de Baixa:</strong> {{ \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y') }}</p>
                            @endif
                            @if ($pagamento->valor_pago)
                                <p><strong>Valor Efetivo Recebido:</strong> <span class="text-green-700 font-bold">R$ {{ number_format($pagamento->valor_pago, 2, ',', '.') }}</span></p>
                            @endif
                            @if ($pagamento->metodo_pagamento)
                                <p><strong>Método:</strong> {{ $pagamento->metodo_pagamento }}</p>
                            @endif
                            <p><strong>Registrado Por:</strong> {{ $pagamento->registradoPor->name ?? 'Sistema' }}</p>
                            @if ($pagamento->observacoes)
                                <p><strong>Observações:</strong> {{ $pagamento->observacoes }}</p>
                            @endif
                        </div>
                        
                        {{-- AÇÃO DE REABRIR --}}
                        @if ($pagamento->status != 'Cancelado') {{-- Se estiver pago, permite reabrir. Se estiver cancelado, talvez você não queira permitir reabrir --}}
                            <form action="{{ route('admin.pagamentos.update', $pagamento->id) }}" method="POST" class="mt-6" onsubmit="return confirm('Tem certeza que deseja REABRIR esta parcela (voltará para Pendente)? Isso deve ser feito em caso de erro no registro.')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="reopen">
                                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                                    Reabrir Parcela (Voltar para Pendente)
                                </button>
                            </form>
                        @endif
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
@endsection