@extends('layouts.app')

@section('title', 'Editar Professor: ' . $professor->nome)

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Editar Professor') . ': ' . $professor->nome }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-bold mb-6 border-b pb-2">Atualizar Informações</h3>

                    {{-- Exibição de Erros de Validação --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.professores.update', ['professor' => $professor->id]) }}">
                        @csrf
                        @method('PUT') {{-- Método HTTP para Atualização --}}

                        <div class="mb-4">
                            <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo do Professor <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   name="nome" 
                                   id="nome" 
                                   value="{{ old('nome', $professor->nome) }}" 
                                   required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('nome')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-start mt-6">
                            {{-- Botão: Atualizar --}}
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                                Atualizar Professor
                            </button>
                            {{-- Botão: Cancelar --}}
                            <a href="{{ route('admin.professores.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 py-2 px-4 rounded-md border border-gray-300 transition duration-150">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection