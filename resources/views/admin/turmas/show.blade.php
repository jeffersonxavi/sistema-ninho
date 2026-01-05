@extends('layouts.app')

@section('title', 'Ver alunos da turma: ' . $turma->nome)

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Lista de Alunos') . ': ' . $turma->nome }}
    </h2>
@endsection
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $turma->nome }}</h2>
                    <p class="text-sm text-gray-600">
                        <strong>Sala:</strong> {{ $turma->sala->nome ?? 'N/A' }} | 
                        <strong>Professores:</strong> {{ $turma->professores->pluck('nome')->implode(', ') }}
                    </p>
                </div>
                <a href="{{ route('admin.turmas.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>

            <div class="p-6">
                <h3 class="font-semibold text-lg mb-4 text-indigo-700">Lista de Alunos Matriculados</h3>
                
                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome do Aluno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CPF</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsável</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($turma->alunos as $aluno)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $aluno->nome_completo }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $aluno->cpf }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $aluno->nome_responsavel }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $aluno->telefone }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                    Nenhum aluno matriculado nesta turma.
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