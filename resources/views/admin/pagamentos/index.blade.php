@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
        <svg class="w-7 h-7 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Contas a Receber (Parcelas)
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-4 sm:p-6 space-y-6">

                    <!-- Mensagem de sucesso -->
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Busca + Filtros -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <form method="GET" class="flex-1 max-w-md">
                            <input type="hidden" name="status" value="{{ $status }}">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" name="aluno" value="{{ request('aluno') }}"
                                       placeholder="Buscar aluno..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </form>

                        <div class="flex flex-wrap gap-2">
                            @foreach($status_options as $option)
                                <a href="{{ route('admin.pagamentos.index', array_merge(request()->except('page'), ['status' => $option])) }}"
                                   class="px-4 py-2 text-sm font-medium rounded-full transition {{ $status === $option ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    {{ $option }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                   <!-- Resumo financeiro - COMPACTO E MODERNO (como na sua imagem) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Previsto -->
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 text-center shadow-sm border border-gray-200">
        <div class="flex flex-col items-center">
            <svg class="w-8 h-8 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
            <p class="text-sm font-medium text-gray-600">Total Previsto</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">
                R$ {{ number_format($totalPrevisto, 2, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Valor Atrasado -->
    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-6 text-center shadow-sm border border-red-200 {{ $totalAtrasado > 0 ? 'ring-2 ring-red-300' : '' }}">
        <div class="flex flex-col items-center">
            <svg class="w-8 h-8 text-red-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-medium text-red-700">Valor Atrasado</p>
            <p class="text-3xl font-extrabold text-red-800 mt-2">
                R$ {{ number_format($totalAtrasado, 2, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Total de Parcelas -->
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-6 text-center shadow-sm border border-indigo-200">
        <div class="flex flex-col items-center">
            <svg class="w-8 h-8 text-indigo-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <p class="text-sm font-medium text-indigo-700">Total de Parcelas</p>
            <p class="text-3xl font-extrabold text-indigo-800 mt-2">
                {{ number_format($totalParcelas, 0, '', '.') }}
            </p>
        </div>
    </div>
</div>

                    <!-- Título da listagem -->
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $status }} <span class="text-gray-500 font-normal">({{ $pagamentos->total() }} parcelas exibidas)</span>
                    </h3>

                    <!-- Tabela -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aluno</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Parcela</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Valor</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Vencimento</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pagamentos as $pagamento)
                                    @php $atrasado = $pagamento->status === 'Atrasado'; @endphp
                                    <tr class="{{ $atrasado ? 'bg-red-25' : 'hover:bg-gray-50' }} transition">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $pagamento->aluno->nome_completo ?? 'Aluno Excluído' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $pagamento->parcela_numero }}/{{ $pagamento->aluno->qtd_parcelas ?? '?' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-green-700">
                                            R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm {{ $atrasado ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                            {{ \Carbon\Carbon::parse($pagamento->data_vencimento)->format('d/m/Y') }}
                                            @if($atrasado)<span class="ml-1 text-xs">(atraso)</span>@endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                                {{ $pagamento->status == 'Pago' ? 'bg-green-100 text-green-800' :
                                                   ($pagamento->status == 'Pendente' ? 'bg-yellow-100 text-yellow-800' :
                                                   ($pagamento->status == 'Atrasado' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ $pagamento->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('admin.pagamentos.show', $pagamento->id) }}"
                                               class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                                Gerenciar →
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                            Nenhuma parcela encontrada para o filtro atual.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $pagamentos->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection