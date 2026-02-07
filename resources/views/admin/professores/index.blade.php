@extends('layouts.app')

@section('title', 'Gerenciamento de Professores')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gerenciamento de Professores') }}
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

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Lista de Professores</h3>

                        {{-- Botão: Criar Novo Professor --}}
                        <a href="{{ route('admin.professores.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                            + Cadastrar Novo Professor
                        </a>
                    </div>

                    {{-- Tabela de Professores --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cadastrado Por</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Cadastro</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($professores as $professor)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $professor->id }}</td>
                                        
                                        {{-- Nome do professor vindo do User --}}
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                            {{ $professor->user ? $professor->user->name : '—' }}
                                        </td>

                                        {{-- Nome do admin que cadastrou --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $professor->cadastradoPor ? $professor->cadastradoPor->name : '—' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $professor->created_at ? $professor->created_at->format('d/m/Y') : '—' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            {{-- Botão: Editar --}}
                                            <a href="{{ route('admin.professores.edit', $professor) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition duration-150">
                                                Editar
                                            </a>

                                            {{-- Botão: Excluir --}}
                                            <form action="{{ route('admin.professores.destroy', $professor) }}" method="POST" class="inline" onsubmit="return confirm('ATENÇÃO: A exclusão do professor também afetará as turmas. Tem certeza que deseja excluir?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition duration-150">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500 py-4">
                                            Nenhum professor cadastrado. Clique em "Cadastrar Novo Professor" para começar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
