@extends('layouts.app')

@section('title', 'Detalhes do Aluno')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Detalhes do Aluno: ') . $aluno->nome_completo }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <h3 class="text-2xl font-bold text-indigo-800">
                            Ficha Completa
                        </h3>
                        <div class="flex space-x-3">
                            
                            {{-- 🚨 NOVO BOTÃO DE VISUALIZAR CONTRATO 🚨 --}}
                            <a href="{{ route('admin.alunos.contrato.download', $aluno->id) }}" 
                               class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150 flex items-center"
                               target="_blank" {{-- Abre em nova aba --}}
                               title="Visualizar Contrato em PDF">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Contrato PDF
                            </a>
                            {{-- FIM NOVO BOTÃO --}}

                            <a href="{{ route('admin.alunos.edit', $aluno->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                                Editar Dados
                            </a>
                            <a href="{{ route('admin.alunos.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                                Voltar
                            </a>
                        </div>
                    </div>

                    {{-- Cartão de Status Geral --}}
                    <div class="mb-8 p-4 rounded-lg shadow-md 
                        @if (Str::contains($status_geral, 'Dívida')) bg-red-100 border-l-4 border-red-500 text-red-800
                        @elseif (Str::contains($status_geral, 'Finalizado/Pago')) bg-green-100 border-l-4 border-green-500 text-green-800
                        @else bg-blue-100 border-l-4 border-blue-500 text-blue-800
                        @endif">
                        <p class="font-bold text-lg">Status Financeiro Geral:</p>
                        <p class="text-xl mt-1">{{ $status_geral }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        {{-- COLUNA 1: DADOS PESSOAIS E CONTATO --}}
                        <div class="border p-4 rounded-lg shadow-sm bg-gray-50">
                            <h4 class="text-lg font-bold mb-3 text-indigo-700">Dados Pessoais e Responsável</h4>
                            <div class="space-y-1 text-sm">
                                <p><strong>Nascimento:</strong> {{ \Carbon\Carbon::parse($aluno->data_nascimento)->format('d/m/Y') }}</p>
                                <p><strong>Responsável:</strong> {{ $aluno->nome_responsavel }}</p>
                                <p><strong>RG:</strong> {{ $aluno->rg ?? 'N/A' }}</p>
                                <p><strong>CPF:</strong> {{ $aluno->cpf ?? 'N/A' }}</p>
                                <p><strong>Telefone:</strong> {{ $aluno->telefone }}</p>
                                <p><strong>Endereço:</strong> {{ $aluno->endereco ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- COLUNA 2: DADOS DO CONTRATO E CURSO --}}
                        <div class="border p-4 rounded-lg shadow-sm bg-gray-50">
                            <h4 class="text-lg font-bold mb-3 text-indigo-700">Detalhes do Contrato</h4>
                            <div class="space-y-1 text-sm">
                                <p><strong>Turma:</strong> {{ $aluno->turma->nome ?? 'N/A' }} (Sala: {{ $aluno->turma->sala->nome ?? 'N/A' }})</p>
                                <p><strong>Matrícula:</strong> {{ \Carbon\Carbon::parse($aluno->data_matricula)->format('d/m/Y') }}</p>
                                <p><strong>Término Previsto:</strong> {{ $aluno->termino_contrato ? \Carbon\Carbon::parse($aluno->termino_contrato)->format('d/m/Y') : 'Contrato Aberto' }}</p>
                                <p><strong>Período:</strong> {{ $aluno->periodo }}</p>
                                <p><strong>Horário:</strong> {{ $aluno->horario ? \Carbon\Carbon::parse($aluno->horario)->format('H:i') : 'Integral/Não definido' }}</p>
                                <p><strong>Dias:</strong> {{ $aluno->dias_da_semana ?? 'N/A' }}</p>
                                <p><strong>Contrato Gerado:</strong> 
                                    <span class="font-semibold {{ $aluno->contrato_gerado ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $aluno->contrato_gerado ? 'Sim' : 'Não' }}
                                    </span>
                                </p>
                                <p class="pt-2 border-t mt-2"><strong>Cadastrado Por:</strong> {{ $aluno->cadastradoPor->name ?? 'Sistema' }}</p>
                            </div>
                        </div>

                        {{-- COLUNA 3: DADOS FINANCEIROS GERAIS --}}
                        <div class="border p-4 rounded-lg shadow-sm bg-gray-50">
                            <h4 class="text-lg font-bold mb-3 text-indigo-700">Resumo Financeiro</h4>
                            <div class="space-y-1 text-sm">
                                <p><strong>Valor Total Contratado:</strong> R$ {{ number_format($aluno->valor_total, 2, ',', '.') }}</p>
                                <p><strong>Valor da Parcela:</strong> R$ {{ number_format($aluno->valor_parcela, 2, ',', '.') }}</p>
                                <p><strong>Quantidade de Parcelas:</strong> {{ $aluno->qtd_parcelas }}</p>
                                <p><strong>Forma de Pagamento Padrão:</strong> {{ $aluno->forma_pagamento }}</p>
                                <p class="pt-2 border-t mt-2"><strong>Parcelas Pagas:</strong> {{ $aluno->pagamentos->where('status', 'Pago')->count() }}</p>
                                <p><strong>Parcelas Pendentes:</strong> {{ $aluno->pagamentos->whereIn('status', ['Pendente', 'Atrasado'])->count() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- SEÇÃO DE PARCELAS --}}
                    <h4 class="text-xl font-bold mt-10 mb-4 border-b pb-2 text-gray-800">
                        Histórico de Pagamentos/Parcelas
                    </h4>

                    @if ($aluno->pagamentos->isEmpty())
                        <p class="text-gray-600 p-4 bg-yellow-50 rounded-lg">
                            Nenhuma parcela financeira foi gerada para este aluno.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previsto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Pago</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Pago</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($aluno->pagamentos as $pagamento)
                                    <tr class="{{ $pagamento->status == 'Atrasado' ? 'bg-red-50' : ($pagamento->status == 'Pago' ? 'bg-green-50' : '') }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pagamento->parcela_numero }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ {{ number_format($pagamento->valor_previsto, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($pagamento->data_vencimento)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if ($pagamento->status == 'Pago') bg-green-200 text-green-900
                                                @elseif ($pagamento->status == 'Pendente') bg-yellow-200 text-yellow-900
                                                @elseif ($pagamento->status == 'Atrasado') bg-red-200 text-red-900
                                                @else bg-gray-200 text-gray-900
                                                @endif">
                                                {{ $pagamento->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pagamento->data_pagamento ? \Carbon\Carbon::parse($pagamento->data_pagamento)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">
                                            {{ $pagamento->valor_pago ? 'R$ ' . number_format($pagamento->valor_pago, 2, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.pagamentos.show', $pagamento->id) }}" class="text-indigo-600 hover:text-indigo-900">Gerenciar Pagamento</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
@endsection