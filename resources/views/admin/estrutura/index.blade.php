@extends('layouts.app')

@section('title', 'Gestão de Turmas e Estrutura')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestão de Turmas e Salas (Admin)') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                
                {{-- Mensagens de Sessão (Sucesso/Erro) --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                
                {{-- ==================================================================== --}}
                {{-- 🚩 1. TABELA DE TURMAS (Principal) --}}
                {{-- ==================================================================== --}}
                
                <div class="mb-10 border-b pb-4">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-indigo-700">📚 Turmas Cadastradas</h3>
                        {{-- Botão: Criar Nova Turma --}}
                        <a href="{{ route('admin.turmas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                            + Cadastrar Nova Turma
                        </a>
                    </div>
                    
                    {{-- Estrutura da Tabela de Turmas (Você precisará criar esta tabela) --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            {{-- ... Colunas: Nome, Sala, Professores, Ações ... --}}
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turma</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sala Designada</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professores</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($turmas as $turma)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $turma->nome }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $turma->sala->nome ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @foreach ($turma->professores as $professor)
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 mr-1 mb-1">{{ $professor->nome }}</span>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <a href="{{ route('admin.turmas.edit', $turma) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                            <form action="{{ route('admin.turmas.destroy', $turma) }}" method="POST" class="inline" onsubmit="return confirm('ATENÇÃO: Excluir a turma removerá todos os vínculos. Tem certeza?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhuma turma cadastrada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- ==================================================================== --}}
                {{-- 🏢 2. TABELA DE SALAS (Sub-Estrutura) --}}
                {{-- ==================================================================== --}}

                <div class="mt-10">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-2xl font-bold text-gray-700">🏢 Salas (Infraestrutura)</h3>
                        {{-- Botão: Criar Nova Sala (Usamos a rota do SalaController) --}}
                        <a href="{{ route('admin.salas.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                            + Cadastrar Nova Sala
                        </a>
                    </div>

                    {{-- Tabela de Salas (Baseado no seu código original) --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome da Sala</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professores Vinculados</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turmas Utilizando</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($salas as $sala)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $sala->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $sala->nome }}</td>
                                        
                                        <td class="px-6 py-4">
                                            @forelse ($sala->professores as $professor)
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 mr-1 mb-1">
                                                    {{ $professor->nome }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500">Nenhum</span>
                                            @endforelse
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            @forelse ($sala->turmas as $turma)
                                                <span class="inline-flex items-center rounded-full bg-pink-100 px-2 py-0.5 text-xs font-medium text-pink-800 mr-1 mb-1">
                                                    {{ $turma->nome }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500">Livre</span>
                                            @endforelse
                                        </td>
                                        
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
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhuma sala cadastrada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection