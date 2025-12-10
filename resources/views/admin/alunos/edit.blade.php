@extends('layouts.app')

@section('title', 'Editar Aluno: ' . $aluno->nome_completo)

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Editar Aluno') . ': ' . $aluno->nome_completo }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.alunos.update', $aluno) }}">
                        @csrf
                        @method('PUT')
                        
                        {{-- O formulário de edição é idêntico ao de criação, mas com preenchimento via $aluno --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- SEÇÃO 1: Dados Pessoais e de Contato --}}
                            <div class="md:col-span-1 border p-4 rounded-lg bg-gray-50">
                                <h4 class="text-lg font-bold mb-4 border-b pb-2 text-indigo-700">Dados do Aluno e Responsável</h4>
                                
                                {{-- Nome Completo --}}
                                <div>
                                    <label for="nome_completo" class="block text-sm font-medium text-gray-700">Nome Completo do Aluno <span class="text-red-500">*</span></label>
                                    <input type="text" name="nome_completo" id="nome_completo" value="{{ old('nome_completo', $aluno->nome_completo) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                
                                {{-- Data Nascimento --}}
                                <div class="mt-4">
                                    <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento <span class="text-red-500">*</span></label>
                                    <input type="date" name="data_nascimento" id="data_nascimento" value="{{ old('data_nascimento', $aluno->data_nascimento?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>

                                {{-- RG e CPF --}}
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="rg" class="block text-sm font-medium text-gray-700">RG</label>
                                        <input type="text" name="rg" id="rg" value="{{ old('rg', $aluno->rg) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                                        <input type="text" name="cpf" id="cpf" value="{{ old('cpf', $aluno->cpf) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                </div>

                                {{-- Nome Responsável --}}
                                <div class="mt-4">
                                    <label for="nome_responsavel" class="block text-sm font-medium text-gray-700">Nome do Responsável <span class="text-red-500">*</span></label>
                                    <input type="text" name="nome_responsavel" id="nome_responsavel" value="{{ old('nome_responsavel', $aluno->nome_responsavel) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                
                                {{-- Telefone e Endereço --}}
                                <div class="mt-4">
                                    <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone de Contato <span class="text-red-500">*</span></label>
                                    <input type="text" name="telefone" id="telefone" value="{{ old('telefone', $aluno->telefone) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="mt-4">
                                    <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço Completo</label>
                                    <input type="text" name="endereco" id="endereco" value="{{ old('endereco', $aluno->endereco) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>

                            {{-- SEÇÃO 2: Dados do Curso e Financeiro --}}
                            <div class="md:col-span-1 border p-4 rounded-lg bg-gray-50">
                                <h4 class="text-lg font-bold mb-4 border-b pb-2 text-indigo-700">Vínculo Turma e Cronograma</h4>

                                {{-- Vínculo Turma --}}
                                <div>
                                    <label for="turma_id" class="block text-sm font-medium text-gray-700">Turma Atribuída <span class="text-red-500">*</span></label>
                                    <select name="turma_id" id="turma_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">Selecione a Turma</option>
                                        @foreach ($turmas as $turma)
                                            <option value="{{ $turma->id }}" {{ old('turma_id', $aluno->turma_id) == $turma->id ? 'selected' : '' }}>
                                                {{ $turma->nome }} (Sala: {{ $turma->sala->nome ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="data_matricula" class="block text-sm font-medium text-gray-700">Data da Matrícula <span class="text-red-500">*</span></label>
                                        <input type="date" name="data_matricula" id="data_matricula" value="{{ old('data_matricula', $aluno->data_matricula?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label for="termino_contrato" class="block text-sm font-medium text-gray-700">Término Contrato (Previsão)</label>
                                        <input type="date" name="termino_contrato" id="termino_contrato" value="{{ old('termino_contrato', $aluno->termino_contrato?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                </div>

                                {{-- Detalhes do Horário --}}
                                <div class="grid grid-cols-3 gap-4 mt-4">
                                    <div>
                                        <label for="periodo" class="block text-sm font-medium text-gray-700">Período <span class="text-red-500">*</span></label>
                                        <select name="periodo" id="periodo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="Manhã" {{ old('periodo', $aluno->periodo) == 'Manhã' ? 'selected' : '' }}>Manhã</option>
                                            <option value="Tarde" {{ old('periodo', $aluno->periodo) == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                                            <option value="Noite" {{ old('periodo', $aluno->periodo) == 'Noite' ? 'selected' : '' }}>Noite</option>
                                            <option value="Integral" {{ old('periodo', $aluno->periodo) == 'Integral' ? 'selected' : '' }}>Integral</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="horario" class="block text-sm font-medium text-gray-700">Horário (Ex: 08:00)</label>
                                        <input type="time" name="horario" id="horario" value="{{ old('horario', $aluno->horario) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label for="dias_da_semana" class="block text-sm font-medium text-gray-700">Dias de Aula</label>
                                        <input type="text" name="dias_da_semana" id="dias_da_semana" value="{{ old('dias_da_semana', $aluno->dias_da_semana) }}" placeholder="Ex: Seg, Qua, Sex" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                </div>
                                
                                <h4 class="text-lg font-bold mt-6 mb-3 border-b pb-1 text-green-700">Detalhes Financeiros (Apenas para Documentação)</h4>
                                <p class="text-xs text-red-500 mb-3">A alteração destes campos NÃO recalcula as parcelas já geradas. Edite as parcelas individualmente na tela 'Financeiro'.</p>

                                {{-- Valores Financeiros --}}
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label for="valor_total" class="block text-sm font-medium text-gray-700">Valor Total (R$) <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" min="0" name="valor_total" id="valor_total" value="{{ old('valor_total', $aluno->valor_total) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label for="qtd_parcelas" class="block text-sm font-medium text-gray-700">Qtd. Parcelas <span class="text-red-500">*</span></label>
                                        <input type="number" min="1" name="qtd_parcelas" id="qtd_parcelas" value="{{ old('qtd_parcelas', $aluno->qtd_parcelas) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                    <div>
                                        <label for="valor_parcela" class="block text-sm font-medium text-gray-700">Valor Parcela (R$) <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" min="0.01" name="valor_parcela" id="valor_parcela" value="{{ old('valor_parcela', $aluno->valor_parcela) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    </div>
                                </div>

                                {{-- Forma de Pagamento --}}
                                <div class="mt-4">
                                    <label for="forma_pagamento" class="block text-sm font-medium text-gray-700">Forma de Pagamento (Padrão) <span class="text-red-500">*</span></label>
                                    <input type="text" name="forma_pagamento" id="forma_pagamento" value="{{ old('forma_pagamento', $aluno->forma_pagamento) }}" required placeholder="Ex: Boleto Recorrente" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>

                        {{-- Botões de Ação --}}
                        <div class="flex items-center justify-start mt-8 border-t pt-4">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                                Atualizar Dados do Aluno
                            </button>
                            <a href="{{ route('admin.alunos.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 py-2 px-4 rounded-md border border-gray-300 transition duration-150">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection