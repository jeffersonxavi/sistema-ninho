@extends('layouts.app')

@section('title', 'Gerenciamento de Turmas')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gerenciamento de Turmas') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Mensagens de Sessão (Sucesso/Erro) --}}
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-lg font-bold">Lista de Turmas</h3>
                        
                        {{-- Botão: Criar Nova Turma --}}
                        <a href="{{ route('admin.turmas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                            + Cadastrar Nova Turma
                        </a>
                    </div>

                    {{-- Tabela de Turmas --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome da Turma</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professores Vinculados</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sala Designada</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($turmas as $turma)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $turma->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $turma->nome }}</td>
                                        
                                        {{-- Professores Vinculados (N:M) --}}
                                        <td class="px-6 py-4">
                                            @forelse ($turma->professores as $professor)
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 mr-1 mb-1">
                                                    {{ $professor->nome }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500">Nenhum professor</span>
                                            @endforelse
                                        </td>
                                        
                                        {{-- Sala Designada (N:1) --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">
                                                {{ $turma->sala->nome ?? 'N/A' }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            {{-- Botão: Editar --}}
                                            <a href="{{ route('admin.turmas.edit', $turma) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition duration-150">
                                                Editar
                                            </a>
                                            
                                            {{-- Botão: Excluir (Formulário) --}}
                                            <form action="{{ route('admin.turmas.destroy', $turma) }}" method="POST" class="inline" onsubmit="return confirm('ATENÇÃO: Excluir esta turma também desvinculará todos os professores e alunos. Tem certeza?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($turmas->isEmpty())
                        <p class="text-center text-gray-500 mt-4">Nenhuma turma cadastrada. Certifique-se de que há Professores e Salas cadastrados para criar uma.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection