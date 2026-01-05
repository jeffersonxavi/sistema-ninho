@extends('layouts.app')

@section('title', 'Alunos da Turma ' . $turma->nome)

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <a href="{{ route('staff.turmas.index') }}" class="text-gray-400 hover:text-indigo-600 transition-colors">Turmas</a>
            <span class="text-gray-300">/</span>
            <span>Lista de Alunos: {{ $turma->nome }}</span>
        </h2>
        <p class="text-gray-600 mt-1">Gerencie e visualize todos os alunos matriculados nesta turma.</p>
    </div>
    <a href="{{ route('staff.turmas.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
        ← Voltar
    </a>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl sm:rounded-3xl overflow-hidden border border-gray-100">
            
            {{-- Cabeçalho da Lista --}}
            <div class="px-8 py-6 bg-gradient-to-r from-indigo-50 to-white border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg font-bold">
                        {{ $turma->alunos->count() }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Alunos Matriculados</h3>
                        <p class="text-sm text-gray-500">Listagem oficial de alunos</p>
                    </div>
                </div>
            </div>

            {{-- Tabela de Alunos --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-8 py-4 text-sm font-bold text-gray-600 uppercase tracking-wider">Aluno</th>
                            <th class="px-8 py-4 text-sm font-bold text-gray-600 uppercase tracking-wider">Matrícula</th>
                            <th class="px-8 py-4 text-sm font-bold text-gray-600 uppercase tracking-wider text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($turma->alunos as $aluno)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full overflow-hidden shadow-md bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center text-indigo-700 font-bold border-2 border-white">
                                        @if($aluno->avatar)
                                            <img src="{{ asset('storage/' . $aluno->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ Str::upper(substr($aluno->nome_completo, 0, 2)) }}
                                        @endif
                                    </div>
                                    <span class="font-bold text-gray-800 group-hover:text-indigo-700 transition-colors">
                                        {{ $aluno->nome_completo }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md font-mono text-sm font-bold">
                                    {{ $aluno->matricula ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="text-indigo-600 hover:text-indigo-900 font-bold text-sm">Ver Perfil</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-5xl mb-4"></span>
                                    <p class="text-xl text-gray-500 font-medium">Nenhum aluno encontrado nesta turma.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection