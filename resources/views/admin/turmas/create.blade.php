@extends('layouts.app')

@section('title', 'Cadastrar Turma')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Cadastrar Nova Turma') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-bold mb-6 border-b pb-2">Detalhes da Nova Turma</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.turmas.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Turma <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   name="nome" 
                                   id="nome" 
                                   value="{{ old('nome') }}" 
                                   required 
                                   placeholder="Ex: Maternal II - Tarde" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        
                        <div class="mb-6">
                            <label for="professores" class="block text-sm font-medium text-gray-700">Professores Vinculados (Selecione um ou mais) <span class="text-red-500">*</span></label>
                            <select name="professores[]" id="professores" multiple required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm h-36">
                                @if ($professores->isEmpty())
                                    <option disabled>É necessário cadastrar professores.</option>
                                @else
                                    @foreach ($professores as $professor)
                                        <option value="{{ $professor->id }}" {{ in_array($professor->id, old('professores', [])) ? 'selected' : '' }}>
                                            {{ $professor->nome }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Mantenha 'Ctrl' ou 'Cmd' pressionado para seleção múltipla.</p>
                        </div>

                        <div class="mb-4">
                            <label for="sala_id" class="block text-sm font-medium text-gray-700">Sala Designada <span class="text-red-500">*</span></label>
                            <select name="sala_id" id="sala_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Selecione uma Sala</option>
                                @foreach ($salas as $sala)
                                    <option value="{{ $sala->id }}" {{ old('sala_id') == $sala->id ? 'selected' : '' }}>
                                        {{ $sala->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($salas->isEmpty()) <p class="text-sm text-red-600 mt-1">É necessário cadastrar uma sala.</p> @endif
                        </div>

                        <div class="flex items-center justify-start mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150">
                                Salvar Turma
                            </button>
                            <a href="{{ route('admin.turmas.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 py-2 px-4 rounded-md border border-gray-300 transition duration-150">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection