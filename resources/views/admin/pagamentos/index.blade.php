@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <i class="fas fa-receipt mr-2 text-indigo-600"></i> {{ __('Contas a Receber (Parcelas)') }}
    </h2>
@endsection

@section('content')
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 transition duration-150 ease-in-out" role="alert">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6 border-b pb-4 flex flex-col md:flex-row justify-between items-center">
                        <form method="GET" action="{{ route('admin.pagamentos.index') }}" class="w-full md:w-1/3 mb-4 md:mb-0">
                            <input type="hidden" name="status" value="{{ $status }}"> 
                            <div class="relative">
                                <input type="text" name="aluno" placeholder="Buscar por nome do aluno..." 
                                       value="{{ request('aluno') }}"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-10">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </form>

                        <div class="flex space-x-2 sm:space-x-4 overflow-x-auto pb-2">
                            @foreach ($status_options as $option)
                                <a href="{{ route('admin.pagamentos.index', array_merge(request()->except('page'), ['status' => $option])) }}" 
                                   class="flex-shrink-0 py-2 px-4 rounded-full text-sm font-medium transition duration-150 ease-in-out whitespace-nowrap 
                                   {{ $status === $option ? 'bg-indigo-600 text-white shadow-lg scale-105' : 'bg-gray-100 text-gray-700 hover:bg-indigo-100 hover:text-indigo-800' }}">
                                    @if ($option === 'Atrasado') <i class="fas fa-clock mr-1"></i> @endif
                                    @if ($option === 'Pago') <i class="fas fa-check-circle mr-1"></i> @endif
                                    @if ($option === 'Pendente') <i class="fas fa-hourglass-half mr-1"></i> @endif
                                    @if ($option === 'Cancelado') <i class="fas fa-times-circle mr-1"></i> @endif
                                    {{ $option }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <h3 class="text-xl font-extrabold mb-4 pb-2 text-indigo-700">
                        Visualizando: <span class="text-gray-900">{{ $status }} ({{ $pagamentos->total() ?? 0 }})</span>
                    </h3>

                    <div class="overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Aluno</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Parcela</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Valor Previsto</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Vencimento</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-indigo-700 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pagamentos as $pagamento)
                                    @php
                                        // Variáveis de estilo
                                        $isAtrasado = $pagamento->status === 'Atrasado';
                                        $rowClass = $isAtrasado ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                                    @endphp
                                    <tr class="{{ $rowClass }} transition duration-150 ease-in-out">
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <i class="fas fa-user-circle mr-1 text-gray-500"></i>
                                            {{ $pagamento->aluno->nome_completo ?? 'Aluno Excluído' }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="font-semibold text-gray-700">{{ $pagamento->parcela_numero }}</span> de {{ $pagamento->aluno->qtd_parcelas ?? '?' }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-700 font-bold">
                                            R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm 
                                            {{ $isAtrasado ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                            @if ($isAtrasado)
                                                <i class="fas fa-exclamation-triangle mr-1 animate-pulse"></i>
                                            @endif
                                            {{ \Carbon\Carbon::parse($pagamento->data_vencimento)->format('d/m/Y') }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full uppercase tracking-wider 
                                                @if ($pagamento->status == 'Pago') bg-green-100 text-green-800
                                                @elseif ($pagamento->status == 'Pendente') bg-yellow-100 text-yellow-800
                                                @elseif ($pagamento->status == 'Atrasado') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $pagamento->status }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <a href="{{ route('admin.pagamentos.show', $pagamento->id) }}" 
                                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm 
                                               text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                                <i class="fas fa-cog mr-2"></i> Gerenciar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 whitespace-nowrap text-base text-center text-gray-500">
                                            <i class="fas fa-folder-open text-2xl mb-2"></i>
                                            <p>Nenhuma parcela encontrada com o status **"{{ $status }}"**.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        {{ $pagamentos->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://kit.fontawesome.com/SEU_CÓDIGO_AQUI.js" crossorigin="anonymous"></script> 
@endpush