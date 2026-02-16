@extends('layouts.app')
@section('title', 'Turmas e Salas')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Gerenciamento de Turmas & Salas') }}
</h2>
@endsection

@section('content')

{{-- 1. LÓGICA DE CORES (Helper Local) --}}
@php
    if (!function_exists('generateColor')) {
        function generateColor($type, $id) {
            // Gera um hash único baseado no tipo (turma/sala/prof) e no ID
            $hash = md5($type . $id);
            // Cria tons pastéis (evita cores muito claras ou muito escuras)
            $r = hexdec(substr($hash, 0, 2)) % 150 + 70;
            $g = hexdec(substr($hash, 2, 2)) % 150 + 70;
            $b = hexdec(substr($hash, 4, 2)) % 150 + 70;
            return "rgb($r,$g,$b)";
        }
    }
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        {{-- 2. ALERTAS DE FEEDBACK --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 border border-green-200" role="alert">
                <div class="ms-3 text-sm font-medium">{{ session('success') }}</div>
                <button @click="show = false" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8">
                    <span class="sr-only">Fechar</span>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
        @endif

        {{-- 3. CONTAINER PRINCIPAL COM ALPINE.JS --}}
        <div class="bg-white shadow-sm sm:rounded-xl border border-gray-200 overflow-hidden" 
             x-data="{ aba: '{{ request()->query('tab', 'turmas') }}' }">
            
            {{-- NAVEGAÇÃO ENTRE ABAS --}}
            <div class="flex border-b border-gray-100 bg-gray-50/50">
                <button @click="aba = 'turmas'" 
                    :class="aba === 'turmas' ? 'border-b-2 border-indigo-500 text-indigo-600 bg-white' : 'text-gray-400 hover:bg-gray-100'"
                    class="flex-1 py-4 text-sm font-bold uppercase tracking-widest transition-all">
                    📚 Lista de Turmas
                </button>
                <button @click="aba = 'salas'" 
                    :class="aba === 'salas' ? 'border-b-2 border-indigo-500 text-indigo-600 bg-white' : 'text-gray-400 hover:bg-gray-100'"
                    class="flex-1 py-4 text-sm font-bold uppercase tracking-widest transition-all">
                    🏢 Gestão de Salas
                </button>
            </div>

            <div class="p-6">
                
                {{-- ================= SEÇÃO: TURMAS ================= --}}
                <div x-show="aba === 'turmas'" x-transition:enter.duration.300ms>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-gray-500 font-black uppercase text-xs tracking-tighter">Registros de Turmas</h3>
                        <a href="{{ route('admin.turmas.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase rounded-lg shadow-sm transition-all active:scale-95">
                            + Nova Turma
                        </a>
                    </div>

                    <div class="relative overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 font-bold">
                                <tr>
                                    <th class="px-6 py-4">Turma & Localização</th>
                                    <th class="px-6 py-4">Corpo Docente</th>
                                    <th class="px-6 py-4 text-center">Alunos</th>
                                    <th class="px-6 py-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($turmas as $turma)
                                <tr class="bg-white hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="px-3 py-1 rounded-md text-white font-bold text-xs shadow-sm w-fit" 
                                                  style="background-color: {{ generateColor('turma', $turma->id) }}">
                                                {{ $turma->nome }}
                                            </span>
                                            @if($turma->sala)
                                                <span class="text-[10px] font-bold uppercase flex items-center gap-1" style="color: {{ generateColor('sala', $turma->sala->id) }}">
                                                    <span class="opacity-50 text-gray-900">📍</span> {{ $turma->sala->nome }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($turma->professores as $prof)
                                                <span class="px-2 py-0.5 rounded border text-[10px] font-bold" 
                                                      style="border-color: {{ generateColor('prof', $prof->id) }}; color: {{ generateColor('prof', $prof->id) }}; background-color: white;">
                                                    {{ $prof->nome }}
                                                </span>
                                            @empty
                                                <span class="text-gray-300 italic text-xs">Sem atribuição</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-gray-100 rounded-full text-xs font-bold text-gray-600">
                                            {{ $turma->alunos->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.turmas.edit', $turma) }}" class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase">Editar</a>
                                        <form action="{{ route('admin.turmas.destroy', $turma) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button onclick="return confirm('Excluir turma?')" class="text-red-400 hover:text-red-600 font-bold text-xs uppercase">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= SEÇÃO: SALAS ================= --}}
                <div x-show="aba === 'salas'" x-transition:enter.duration.300ms>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-gray-500 font-black uppercase text-xs tracking-tighter">Registros de Salas</h3>
                        <a href="{{ route('admin.salas.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase rounded-lg shadow-sm transition-all active:scale-95">
                            + Nova Sala
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($salas as $sala)
                        <div class="border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-4 py-1.5 rounded-lg text-white font-black text-sm" 
                                      style="background-color: {{ generateColor('sala', $sala->id) }}">
                                    {{ $sala->nome }}
                                </span>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.salas.edit', $sala) }}" class="p-1 text-gray-400 hover:text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Turmas nesta sala:</p>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($sala->turmas as $t)
                                        <span class="px-2 py-1 rounded text-[10px] font-bold border" 
                                              style="border-color: {{ generateColor('turma', $t->id) }}; color: {{ generateColor('turma', $t->id) }}">
                                            {{ $t->nome }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-300 italic">Nenhuma turma alocada</s pan>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection