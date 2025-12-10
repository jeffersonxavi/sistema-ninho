@extends('layouts.app')

@section('title', 'Gerenciamento de Salas')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gerenciamento de Salas') }}
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
                        <h3 class="text-lg font-bold">Lista de Salas</h3>
                        
                        {{-- Botão: Criar Nova Sala --}}
                        <a href="{{ route('admin.salas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                            + Cadastrar Nova Sala
                        </a>
                    </div>

                    {{-- Tabela de Salas --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome da Sala</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professores Vinculados</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turmas Utilizando</th> <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cadastrado Por</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($salas as $sala)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $sala->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $sala->nome }}</td>
                                        
                                        {{-- Exibe os Professores Vinculados (Tags/Badges) --}}
                                        <td class="px-6 py-4">
                                            @forelse ($sala->professores as $professor)
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 mr-1 mb-1">
                                                    {{ $professor->nome }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500">Nenhum</span>
                                            @endforelse
                                        </td>
                                        
                                        {{-- Exibe as Turmas Utilizando a Sala (Tags/Badges) --}}
                                        <td class="px-6 py-4">
                                            @forelse ($sala->turmas as $turma)
                                                <span class="inline-flex items-center rounded-full bg-pink-100 px-2 py-0.5 text-xs font-medium text-pink-800 mr-1 mb-1">
                                                    {{ $turma->nome }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500">Livre</span>
                                            @endforelse
                                        </td>
                                        

                                        <td class="px-6 py-4 whitespace-nowrap">{{ $sala->cadastradoPor->name }}</td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            {{-- Botão: Editar --}}
                                            <a href="{{ route('admin.salas.edit', $sala) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition duration-150">
                                                Editar
                                            </a>
                                            
                                            {{-- Botão: Excluir (Formulário) --}}
                                            <form action="{{ route('admin.salas.destroy', $sala) }}" method="POST" class="inline" onsubmit="return confirm('ATENÇÃO: Excluir a sala pode afetar as turmas. Tem certeza?');">
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
                    @if ($salas->isEmpty())
                        <p class="text-center text-gray-500 mt-4">Nenhuma sala cadastrada. Clique em "Cadastrar Nova Sala" para começar.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection