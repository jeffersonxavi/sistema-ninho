@extends('layouts.app')

@section('title', 'Lista de Alunos')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestão de Alunos') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">Alunos Matriculados ({{ $alunos->count() }})</h3>
                        <a href="{{ route('admin.alunos.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                            + Novo Aluno
                        </a>
                    </div>
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aluno</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turma/Período</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matrícula</th>
                                    {{-- COLUNA ADICIONADA: STATUS FINANCEIRO --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Financeiro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($alunos as $aluno)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $aluno->nome_completo }}
                                        <p class="text-xs text-gray-500">
                                            Resp.: {{ $aluno->responsavel->nome ?? 'N/A' }}
                                            ({{ $aluno->responsavel->telefone ?? 'N/A' }})
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $aluno->turma->nome ?? 'N/A' }}
                                        <p class="text-xs text-gray-500">{{ $aluno->periodo }} ({{ $aluno->horario ? \Carbon\Carbon::parse($aluno->horario)->format('H:i') : 'Horário não definido' }})</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($aluno->data_matricula)->format('d/m/Y') }}
                                        <p class="text-xs text-gray-500">Valor Mensal: R$ {{ number_format($aluno->valor_parcela, 2, ',', '.') }}</p>
                                    </td>
                                    
                                    {{-- TRECHO A SER SUBSTITUÍDO NA COLUNA "Financeiro" --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $status = $aluno->status_financeiro_geral ?? 'N/A';
                                            $cor = match ($status) {
                                                'Atrasado' => 'bg-red-100 text-red-800',           // 🚨 PRIORIDADE MÁXIMA
                                                'Em Curso (Pendente)' => 'bg-yellow-100 text-yellow-800', // 🟡 Parcelas a vencer
                                                'Quitado (Pago)' => 'bg-green-100 text-green-800',      // ✅ Tudo pago
                                                default => 'bg-gray-100 text-gray-800',                // ⚪ Inconsistência
                                            };
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cor }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    
                                    {{-- COLUNA DE AÇÕES COM BOTÕES/ÍCONES --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            
                                            {{-- 1. BOTÃO PDF (Visualizar Contrato) --}}
                                            @if ($aluno->contrato_gerado)
                                                <a href="{{ route('admin.alunos.contrato.download', $aluno->id) }}" 
                                                class="p-2 text-white bg-red-600 hover:bg-red-700 rounded-full shadow-md transition duration-150" 
                                                title="Visualizar Contrato PDF"
                                                target="_blank">
                                                    {{-- Ícone de PDF/Documento --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </a>
                                            @endif

                                            {{-- 2. BOTÃO DETALHES (Ver Detalhes) --}}
                                            <a href="{{ route('admin.alunos.show', $aluno->id) }}" 
                                            class="p-2 text-white bg-blue-600 hover:bg-blue-700 rounded-full shadow-md transition duration-150" 
                                            title="Ver Detalhes do Aluno">
                                                {{-- Ícone de Olho/Visualizar --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            {{-- 3. BOTÃO EDITAR --}}
                                            <a href="{{ route('admin.alunos.edit', $aluno->id) }}" 
                                            class="p-2 text-white bg-yellow-500 hover:bg-yellow-600 rounded-full shadow-md transition duration-150" 
                                            title="Editar Aluno">
                                                {{-- Ícone de Lápis/Editar --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>

                                            {{-- 4. BOTÃO EXCLUIR (usando FORM com botão) --}}
                                            <form action="{{ route('admin.alunos.destroy', $aluno->id) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este aluno e todos os seus pagamentos? Esta ação não pode ser desfeita.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 text-white bg-gray-500 hover:bg-gray-600 rounded-full shadow-md transition duration-150"
                                                        title="Excluir Aluno">
                                                    {{-- Ícone de Lixeira/Excluir --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    {{-- Colspan ajustado para 6 colunas --}}
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        Nenhum aluno cadastrado.
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