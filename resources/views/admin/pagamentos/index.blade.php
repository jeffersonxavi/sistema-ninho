@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('header')
    <h2 class="font-bold text-2xl text-slate-800 leading-tight flex items-center gap-3">
        <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-200">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <span>Financeiro <span class="text-slate-400 font-normal">|</span> Contas a Receber</span>
    </h2>
@endsection

@section('content')
    <div class="py-8 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-900 px-4 py-3 rounded-r-lg shadow-sm flex items-center animate-in fade-in slide-in-from-top-2">
                    <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex items-center gap-5">
                    <div class="p-4 bg-slate-100 rounded-2xl text-slate-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Previsto</p>
                        <p class="text-2xl font-black text-slate-800">R$ {{ number_format($totalPrevisto, 2, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border {{ $totalAtrasado > 0 ? 'border-rose-200 ring-4 ring-rose-50' : 'border-slate-200' }} flex items-center gap-5">
                    <div class="p-4 {{ $totalAtrasado > 0 ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-600' }} rounded-2xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-rose-400 uppercase tracking-widest">Atrasado</p>
                        <p class="text-2xl font-black text-rose-600">R$ {{ number_format($totalAtrasado, 2, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex items-center gap-5">
                    <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-indigo-400 uppercase tracking-widest">Quantidade</p>
                        <p class="text-2xl font-black text-indigo-800">{{ number_format($totalParcelas, 0, '', '.') }} <span class="text-sm font-medium text-indigo-400">parcela(s).</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-xl rounded-[2rem] border border-slate-200 overflow-hidden transition-all">
                <div class="p-6 sm:p-8 space-y-6">

                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                        <form method="GET" class="w-full lg:max-w-md">
                            <input type="hidden" name="status" value="{{ $status }}">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" name="aluno" value="{{ request('aluno') }}"
                                       placeholder="Pesquisar por nome do aluno..."
                                       class="w-full pl-11 pr-4 py-3 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-medium text-slate-700 placeholder-slate-400 shadow-inner">
                            </div>
                        </form>

                        <div class="flex flex-wrap items-center gap-2 bg-slate-100 p-1.5 rounded-2xl">
                            @foreach($status_options as $option)
                                <a href="{{ route('admin.pagamentos.index', array_merge(request()->except('page'), ['status' => $option])) }}"
                                   class="px-5 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition-all {{ $status === $option ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-indigo-600 hover:bg-white/50' }}">
                                    {{ $option }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Aluno</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Parcela</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Valor</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Vencimento</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-400 uppercase tracking-widest">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-black text-slate-400 uppercase tracking-widest">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-50">
                                @forelse($pagamentos as $pagamento)
                                    @php
                                        $atrasado = $pagamento->status !== 'Pago' && \Carbon\Carbon::parse($pagamento->data_vencimento)->isPast();
                                    @endphp
                                    <tr class="group hover:bg-slate-50 transition-all {{ $atrasado ? 'bg-rose-50/30' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800">{{ $pagamento->aluno->nome_completo ?? 'Aluno Excluído' }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium">ID: #{{ $pagamento->id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">
                                                {{ $pagamento->parcela_numero }}/{{ $pagamento->aluno->qtd_parcelas ?? '?' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-black text-emerald-600">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm {{ $atrasado ? 'text-rose-600 font-black' : 'text-slate-600 font-medium' }}">
                                                {{ \Carbon\Carbon::parse($pagamento->data_vencimento)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter
                                            {{ $pagamento->status === 'Pago' ? 'bg-emerald-100 text-emerald-700' :
                                            ($atrasado ? 'bg-rose-100 text-rose-700 animate-pulse' :
                                            'bg-amber-100 text-amber-700') }}">
                                                <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $pagamento->status === 'Pago' ? 'bg-emerald-500' : ($atrasado ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                                                {{ $pagamento->status === 'Pago' ? 'Recebido' : ($atrasado ? 'Atrasado' : 'Pendente') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('admin.pagamentos.show', $pagamento->id) }}"
                                               class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                                                Gerenciar
                                                <svg class="w-3 h-3 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 4-8-4"/></svg>
                                                <p class="text-slate-400 font-medium italic">Nenhuma parcela encontrada para este filtro.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $pagamentos->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection