@extends('layouts.app')

@section('title', 'Minhas Turmas')

@section('header')
<h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-3">
    <span class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
        👨‍🏫
    </span>
    Minhas Turmas e Alunos
</h2>
<p class="text-gray-600 mt-2">Olá, <strong>{{ Auth::user()->name }}</strong>! Aqui estão suas turmas e todos os alunos matriculados nela.</p>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl">

            <div class="p-6 sm:p-10 text-gray-900">

                {{-- =================================== MINHAS TURMAS COM ALUNOS =================================== --}}
                <div class="mb-16">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-3xl font-black text-pink-800 flex items-center gap-4">
                            <span class="w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl">
                                📚
                            </span>
                            Minhas Turmas
                            <span class="ml-4 px-5 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white text-lg font-extrabold rounded-2xl shadow-lg">
                                {{ $turmas->count() }}
                            </span>
                        </h3>
                    </div>

                    @if($turmas->isEmpty())
                    <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-dashed border-gray-300">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-xl text-gray-600 font-semibold">Você ainda não está vinculado a nenhuma turma.</p>
                        <p class="text-gray-500 mt-2">Fale com o administrador para ser adicionado a uma turma.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($turmas as $turma)
                        <div class="group bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden border-t-8 {{ $turma->sala ? 'border-yellow-500' : 'border-red-500' }} flex flex-col h-full hover:-translate-y-2">
                            <div class="p-6 flex-grow">
                                <h4 class="text-2xl font-black text-gray-900 mb-4 truncate">{{ $turma->nome }}</h4>

                                <div class="mb-6">
                                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-2">Localização</p>
                                    @if($turma->sala)
                                    <div class="flex items-center gap-3 bg-yellow-50 px-4 py-3 rounded-xl border border-yellow-200">
                                        <span class="text-xl">🏫</span>
                                        <span class="font-bold text-yellow-900">{{ $turma->sala->nome }}</span>
                                    </div>
                                    @else
                                    <div class="flex items-center gap-3 bg-red-50 px-4 py-3 rounded-xl border border-red-200">
                                        <span class="text-xl">⚠️</span>
                                        <span class="font-bold text-red-900">Sem sala</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl">
                                    <span class="text-gray-600 font-medium">Total de Alunos:</span>
                                    <span class="px-4 py-1 bg-indigo-600 text-white font-bold rounded-lg text-lg">
                                        {{ $turma->alunos->count() }}
                                    </span>
                                </div>
                            </div>

                            {{-- BOTÃO VER ALUNOS --}}
                        <div class="p-4 bg-gray-50 border-t border-gray-100 mt-auto">
                            <a href="{{ route('staff.turmas.alunos', $turma->id) }}" 
                            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all transform active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Ver Lista de Alunos
                            </a>
                        </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- =================================== SALAS (SECUNDÁRIA) =================================== --}}
                <div class="pt-12 border-t-4 border-dashed border-gray-200">
                    <h3 class="text-2xl font-bold text-indigo-800 mb-8 flex items-center gap-4">
                        <span class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-xl shadow-lg">
                            🏢
                        </span>
                        Salas Disponíveis na Escola
                        <span class="ml-3 px-4 py-2 bg-indigo-100 text-indigo-800 font-bold rounded-full text-sm">
                            {{ $salas->count() }}
                        </span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @forelse($salas as $sala)
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-6 rounded-2xl border border-indigo-200 shadow-lg hover:shadow-xl transition">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow">
                                    🏫
                                </div>
                                <h4 class="font-bold text-indigo-900 text-lg truncate">{{ $sala->nome }}</h4>
                            </div>

                            <p class="text-sm text-gray-700 mb-2">
                                <span class="font-semibold">Turmas:</span> <span class="font-bold text-indigo-700">{{ $sala->turmas->count() }}</span>
                            </p>

                            @if($sala->turmas->isNotEmpty())
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach($sala->turmas->take(3) as $t)
                                <span class="text-xs bg-pink-100 text-pink-700 px-3 py-1 rounded-full font-medium">{{ $t->nome }}</span>
                                @endforeach
                                @if($sala->turmas->count() > 3)
                                <span class="text-xs text-gray-500">+{{ $sala->turmas->count() - 3 }}</span>
                                @endif
                            </div>
                            @endif

                            @if(isset($sala->capacidade))
                            <p class="text-xs text-gray-600 mt-4 text-right">
                                Capacidade: <strong class="text-indigo-800">{{ $sala->capacidade }} alunos</strong>
                            </p>
                            @endif
                        </div>
                        @empty
                        <div class="lg:col-span-4 text-center py-10 text-gray-500">
                            Nenhuma sala cadastrada.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Scrollbar customizada -->
<style>
    .scrollbar-thin::-webkit-scrollbar { width: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: #f3f4f6; border-radius: 3px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 3px; }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #6b7280; }
</style>
@endsection