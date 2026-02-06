@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <i class="fas fa-receipt mr-2 text-indigo-600"></i> {{ __('Contas a Receber (Staff)') }}
    </h2>
@endsection

@section('content')
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6 border-b pb-4 flex flex-col md:flex-row justify-between items-center">
                        <form method="GET" action="{{ route('staff.pagamentos.index') }}" class="w-full md:w-1/3 mb-4 md:mb-0">
                            <input type="hidden" name="status" value="{{ $status }}"> 
                            <div class="relative">
                                <input type="text" name="aluno" placeholder="Buscar aluno..." 
                                       value="{{ request('aluno') }}"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-10">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </form>

                        <div class="flex space-x-2 overflow-x-auto pb-2">
                            @foreach ($status_options as $option)
                                <a href="{{ route('staff.pagamentos.index', array_merge(request()->except('page'), ['status' => $option])) }}" 
                                   class="flex-shrink-0 py-2 px-4 rounded-full text-sm font-medium transition duration-150
                                   {{ $status === $option ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-indigo-100' }}">
                                    {{ $option }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase">Aluno</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase">Parcela</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Valor Previsto</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase">Vencimento</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pagamentos as $pagamento)
                                    @php
                                        // Lógica para identificar atraso real
                                        $vencimento = \Carbon\Carbon::parse($pagamento->data_vencimento);
                                        $estaAtrasado = $pagamento->status === 'Pendente' && $vencimento->isPast() && !$vencimento->isToday();
                                    @endphp
                                    <tr class="hover:bg-gray-50 {{ $estaAtrasado ? 'bg-red-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $pagamento->aluno->nome_completo ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $pagamento->parcela_numero }}ª Parcela
                                        </td>
                                         <td class="px-6 py-4 whitespace-nowrap text-sm text-green-700 font-bold">
                                            R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="{{ $estaAtrasado ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                {{ $vencimento->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($estaAtrasado)
                                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                    Atrasado
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded-full text-xs font-bold
                                                    @if($pagamento->status == 'Pago') bg-green-100 text-green-800
                                                    @elseif($pagamento->status == 'Pendente') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $pagamento->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <a href="{{ route('staff.pagamentos.show', $pagamento->id) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                                <i class="fas fa-cog mr-1"></i> Gerenciar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            Nenhum registro encontrado para o status <strong>{{ $status }}</strong>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pagamentos->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection