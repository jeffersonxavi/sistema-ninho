@extends('layouts.app')

@section('title', 'Editar Professor: ' . ($professor->user->name ?? '—'))

@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.professores.index') }}" class="text-gray-400 hover:text-indigo-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Professor') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="py-12" x-data="{ maskCpf: '999.999.999-99', maskTel: '(99) 99999-9999' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.professores.update', $professor) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white shadow-sm sm:rounded-xl border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Dados Pessoais
                        </h3>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-6 gap-6">
                        <div class="md:col-span-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $professor->user->name ?? '') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Nascimento</label>
                            {{-- Procure o campo de nascimento e mude o value para: --}}
                            <input type="date" name="data_nascimento" id="data_nascimento"
                                value="{{ old('data_nascimento', optional($professor->user->data_nascimento)->format('Y-m-d') ?? '') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="md:col-span-3">
                            <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                            <input type="text" name="cpf" id="cpf" x-mask="maskCpf"
                                placeholder="000.000.000-00" value="{{ old('cpf', $professor->user->cpf ?? '') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('cpf')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-3">
                            <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" name="telefone" id="telefone" x-mask="maskTel"
                                placeholder="(00) 00000-0000"
                                value="{{ old('telefone', $professor->user->telefone ?? '') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-xl border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Financeiro
                        </h3>
                    </div>
                    <div class="p-6">
                        <label for="chave_pix" class="block text-sm font-medium text-gray-700">Chave PIX</label>
                        <div class="mt-1 relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 sm:text-sm">🔑</span>
                            </div>
                            <input type="text" name="chave_pix" id="chave_pix"
                                value="{{ old('chave_pix', $professor->chave_pix ?? '') }}"
                                class="pl-10 block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="E-mail, CPF ou Chave Aleatória">
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-xl border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Segurança e Acesso
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">E-mail Institucional
                                <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', $professor->user->email ?? '') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('email')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Alterar Senha <span
                                        class="text-xs text-gray-400 font-normal">(deixe vazio para manter)</span></label>
                                <input type="password" name="password" id="password"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="••••••••">
                                @error('password')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between bg-gray-100 p-4 rounded-xl border border-gray-200">
                    <p class="text-xs text-gray-500 px-2">
                        Última atualização: {{ $professor->updated_at->format('d/m/Y H:i') }}
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.professores.index') }}"
                            class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="px-10 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-md shadow-indigo-200">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection