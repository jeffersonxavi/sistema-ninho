@extends('layouts.app')
@section('title', 'Gerenciar Pagamento')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Financeiro') }} <span class="text-gray-400 mx-2">|</span> {{ __('Gerenciar Parcela') }}
    </h2>
@endsection

@section('content')
@php
    $vencimento = \Carbon\Carbon::parse($pagamento->data_vencimento);
    $estaAtrasado = $pagamento->status === 'Pendente' && $vencimento->isPast();
    $statusExibicao = $estaAtrasado ? 'Atrasado' : $pagamento->status;

    $colors = [
        'Pago'      => ['bg-emerald-100', 'text-emerald-800', 'dot' => 'bg-emerald-500'],
        'Pendente'  => ['bg-amber-100', 'text-amber-800', 'dot' => 'bg-amber-500'],
        'Atrasado'  => ['bg-rose-100', 'text-rose-800', 'dot' => 'bg-rose-500'],
        'Cancelado' => ['bg-slate-100', 'text-slate-600', 'dot' => 'bg-slate-400'],
    ][$statusExibicao] ?? ['bg-gray-100', 'text-gray-700', 'dot' => 'bg-gray-400'];
@endphp

<div class="py-6 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white shadow-xl rounded-[2rem] border border-slate-200 overflow-hidden">
            {{-- Header Compacto --}}
            <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap justify-between items-center gap-4 bg-white">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="p-2 bg-indigo-600 rounded-xl text-white shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute h-full w-full rounded-full {{ $colors['dot'] }} opacity-40"></span>
                            <span class="relative rounded-full h-3 w-3 {{ $colors['dot'] }} border-2 border-white"></span>
                        </span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-slate-900">Parcela #{{ $pagamento->parcela_numero }}</h3>
                            {{-- Aumentado de 9px para xs (12px) --}}
                            <span class="px-2 py-0.5 rounded-full text-xs font-black uppercase tracking-wider {{ $colors[0] }} {{ $colors[1] }}">
                                {{ $statusExibicao }}
                            </span>
                        </div>
                        {{-- Aumentado de 11px para sm (14px) --}}
                        <p class="text-sm text-slate-500 font-medium">Aluno: <span class="text-indigo-600 font-bold">{{ $pagamento->aluno->nome_completo ?? 'Não Identificado' }}</span></p>
                    </div>
                </div>
                {{-- Aumentado de 11px para sm (14px) --}}
                <a href="{{ route('admin.pagamentos.index') }}" class="group text-sm font-bold text-slate-400 hover:text-indigo-600 transition-all flex items-center">
                    <svg class="w-3 h-3 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> 
                    Voltar
                </a>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex justify-between items-center transition-all hover:border-indigo-200">
                        <div>
                            {{-- Aumentado de 9px para xs (12px) --}}
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest block">Valor Previsto</span>
                            <p class="text-xl font-black text-slate-800">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</p>
                        </div>
                        <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg hidden sm:block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex justify-between items-center transition-all hover:border-rose-200">
                        <div>
                            {{-- Aumentado de 9px para xs (12px) --}}
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest block">Vencimento</span>
                            <p class="text-xl font-black @if($estaAtrasado) text-rose-600 @else text-slate-800 @endif">
                                {{ $vencimento->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="p-2 @if($estaAtrasado) bg-rose-100 text-rose-600 @else bg-slate-200 text-slate-600 @endif rounded-lg hidden sm:block">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                        </div>
                    </div>
                </div>

                @if (in_array($pagamento->status, ['Pendente', 'Atrasado']))
                    <div class="bg-indigo-50/40 p-6 rounded-[1.5rem] border border-indigo-100">
                        {{-- Aumentado de 10px para xs (12px) --}}
                        <h4 class="text-xs font-black text-indigo-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                            Efetuar Baixa no Sistema
                        </h4>

                        <form method="POST" action="{{ route('admin.pagamentos.update', $pagamento->id) }}" class="space-y-4">
                            @csrf @method('PUT')
                            <input type="hidden" name="action" value="pay">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    {{-- Aumentado de 10px para xs (12px) --}}
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Valor Recebido (R$)</label>
                                    <input type="number" step="0.01" id="input_valor_pago" name="valor_pago" 
                                           value="{{ old('valor_pago', $pagamento->valor_previsto) }}" required 
                                           oninput="checkValor(this.value, {{ $pagamento->valor_previsto }})"
                                           class="w-full h-14 px-4 rounded-xl border-2 border-white focus:border-indigo-500 focus:ring-0 text-lg font-black text-slate-800 shadow-sm transition-all">
                                    {{-- Aumentado de 9px para xs (12px) --}}
                                    <p id="aviso_valor" class="hidden text-xs text-amber-600 font-bold italic ml-1">⚠️ Valor diferente do previsto.</p>
                                </div>
                                <div class="space-y-1">
                                    {{-- Aumentado de 10px para xs (12px) --}}
                                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Data do Recebimento</label>
                                    <input type="date" name="data_pagamento" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required 
                                        class="w-full h-14 px-4 rounded-xl border-2 border-white focus:border-indigo-500 focus:ring-0 text-sm font-bold text-slate-700 shadow-sm">
                                </div>
                            </div>

                            <div class="space-y-2">
                                {{-- Aumentado de 10px para xs (12px) --}}
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Forma de Pagamento</label>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach(['PIX', 'Cartão', 'Dinheiro', 'Boleto'] as $m)
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="metodo_pagamento" value="{{ $m }}" class="peer hidden" {{ $loop->first ? 'checked' : '' }}>
                                            {{-- Aumentado de 10px para xs (12px) --}}
                                            <div class="py-2 text-center rounded-lg border-2 border-transparent bg-white text-slate-400 font-black text-xs peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white shadow-sm transition-all uppercase tracking-tighter">
                                                {{ $m }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="space-y-1">
                                {{-- Aumentado de 10px para xs (12px) --}}
                                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Observações</label>
                                <textarea name="observacoes" rows="2" placeholder="Notas internas..." 
                                    class="w-full px-4 py-2 rounded-xl border-2 border-white focus:border-indigo-500 focus:ring-0 text-xs italic text-slate-600 shadow-sm transition-all"></textarea>
                            </div>

                            <button type="submit" class="w-full h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg shadow-indigo-100 font-black uppercase tracking-widest transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                                Registrar Recebimento
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Visualização de Parcela Concluída --}}
                    <div class="bg-slate-50 rounded-[1.5rem] border border-slate-200 p-6 relative overflow-hidden">
                        <div class="absolute -top-4 -right-4 w-24 h-24 opacity-10">
                            <svg class="w-full h-full @if($pagamento->status == 'Pago') text-emerald-600 @else text-slate-400 @endif" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>

                        <div class="max-w-md mx-auto relative z-10">
                            <div class="text-center mb-4">
                                {{-- Aumentado de 9px para xs (12px) --}}
                                <span class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Comprovante Digital</span>
                                <h4 class="text-xl font-black text-slate-800 mt-1">Recibo de Pagamento</h4>
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center py-2 border-b border-slate-200 border-dashed">
                                    {{-- Aumentado de 10px para xs (12px) --}}
                                    <span class="text-xs font-bold text-slate-400 uppercase">Data da Baixa</span>
                                    <span class="font-bold text-slate-700">{{ $pagamento->data_pagamento ? \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y') : '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-200 border-dashed">
                                    {{-- Aumentado de 10px para xs (12px) --}}
                                    <span class="text-xs font-bold text-slate-400 uppercase">Método</span>
                                    <span class="px-2 py-0.5 bg-slate-200 rounded text-xs font-black text-slate-700 uppercase">{{ $pagamento->metodo_pagamento ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center p-4 bg-white rounded-xl shadow-sm border border-slate-100 mt-4">
                                    {{-- Aumentado de 10px para xs (12px) --}}
                                    <span class="text-xs font-black text-slate-400 uppercase">Valor Liquidado</span>
                                    <span class="font-black text-emerald-600 text-2xl">R$ {{ number_format($pagamento->valor_pago, 2, ',', '.') }}</span>
                                </div>

                                @if($pagamento->observacoes)
                                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-100 mt-4 text-sm">
                                        <p class="italic text-amber-800 leading-relaxed font-medium">"{{ $pagamento->observacoes }}"</p>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-8 flex flex-col gap-2">
                                <button onclick="window.print()" class="w-full py-3 bg-slate-800 text-white rounded-xl font-black uppercase text-xs tracking-widest hover:bg-slate-900 transition-all flex justify-center items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Imprimir
                                </button>

                                <form action="{{ route('admin.pagamentos.update', $pagamento->id) }}" method="POST" class="w-full" onsubmit="return confirm('Confirmar estorno?')">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="action" value="reopen">
                                    {{-- Aumentado de 9px para xs (12px) --}}
                                    <button class="w-full py-2 text-xs font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest transition-all">
                                        Estornar e Reabrir Parcela
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
                
                {{-- Seção de Cancelamento --}}
                @if (in_array($pagamento->status, ['Pendente', 'Atrasado']))
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <div id="cancelArea" class="text-center">
                        {{-- Aumentado de 9px para xs (12px) --}}
                        <button type="button" onclick="toggleCancel(true)" id="btnCancelTrigger" class="text-xs font-black text-slate-300 hover:text-rose-500 uppercase tracking-[0.2em] transition-colors">
                            Suspender ou Cancelar Parcela
                        </button>
                        
                        <form id="formCancel" action="{{ route('admin.pagamentos.update', $pagamento->id) }}" method="POST" class="hidden mt-4 animate-in fade-in slide-in-from-top-2 duration-300">
                            @csrf @method('PUT')
                            <input type="hidden" name="action" value="cancel">
                            <div class="bg-rose-50 p-4 rounded-2xl border border-rose-100 max-w-md mx-auto">
                                <textarea name="observacoes" required placeholder="Motivo do cancelamento..." 
                                    class="w-full rounded-xl border-none focus:ring-0 bg-white/50 text-xs italic text-rose-900 placeholder-rose-300 mb-3"></textarea>
                                <div class="flex gap-2">
                                    {{-- Aumentado de 10px para xs (12px) --}}
                                    <button type="submit" class="flex-1 py-3 bg-rose-600 text-white rounded-lg text-xs font-black uppercase tracking-widest hover:bg-rose-700 shadow-md">Confirmar</button>
                                    <button type="button" onclick="toggleCancel(false)" class="px-4 py-3 bg-white text-slate-500 rounded-lg text-xs font-bold uppercase border border-slate-200">Sair</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCancel(show) {
        document.getElementById('btnCancelTrigger').classList.toggle('hidden', show);
        document.getElementById('formCancel').classList.toggle('hidden', !show);
    }

    function checkValor(digitado, previsto) {
        const aviso = document.getElementById('aviso_valor');
        if (parseFloat(digitado) !== previsto && digitado !== "") {
            aviso.classList.remove('hidden');
        } else {
            aviso.classList.add('hidden');
        }
    }
</script>

<style>
    @media print {
        body * { visibility: hidden; }
        .bg-slate-50.rounded-\[1\.5rem\] { visibility: visible; position: absolute; left: 0; top: 0; width: 100%; border: none; }
        .bg-slate-50.rounded-\[1\.5rem\] * { visibility: visible; }
        button, form { display: none !important; }
    }
</style>
@endsection