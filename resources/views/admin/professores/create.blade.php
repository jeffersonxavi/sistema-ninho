@extends('layouts.app')

@section('title', 'Cadastrar Professor')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Cadastrar Novo Professor') }}
    </h2>
@endsection

@section('content')
<div class="py-12" x-data="formValidation()">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
            <div class="p-8">
                
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800">Detalhes do Novo Professor</h3>
                    <p class="text-sm text-gray-500">O sistema validará os dados conforme você preenche.</p>
                </div>

                <form method="POST" action="{{ route('admin.professores.store') }}" @submit.prevent="submitForm">
                    @csrf

                    <div class="space-y-6">
                        {{-- Nome Completo --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nome Completo <span class="text-red-500">*</span></label>
                            <input type="text" name="nome" x-model="fields.nome" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 border"
                                   :class="errors.nome ? 'border-red-500' : 'border-gray-300'">
                            <p x-show="errors.nome" class="text-red-500 text-xs mt-1" x-text="errors.nome"></p>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Seção de Acesso --}}
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <h4 class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-4">Credenciais de Acesso</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Email --}}
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Email Institucional</label>
                                    <input type="email" name="email" x-model="fields.email" @blur="validateEmail"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 border"
                                           placeholder="professor@escola.com">
                                    <p x-show="errors.email" class="text-red-500 text-xs mt-1" x-text="errors.email"></p>
                                </div>

                                {{-- Senha --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Senha</label>
                                    <div class="mt-1 relative">
                                        <input :type="showPasswords ? 'text' : 'password'" name="password" 
                                               x-model="fields.password" @input="validatePasswords"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 border pr-10">
                                        
                                        <button type="button" @click="showPasswords = !showPasswords" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                                            <template x-if="!showPasswords">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </template>
                                            <template x-if="showPasswords">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                            </template>
                                        </button>
                                    </div>
                                    {{-- Barra de Força --}}
                                    <div class="mt-2 h-1 w-full bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full transition-all duration-500" 
                                             :class="passwordStrengthColor" 
                                             :style="`width: ${passwordStrengthWidth}%` text-align: right"></div>
                                    </div>
                                </div>

                                {{-- Confirmar Senha --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                                    <input :type="showPasswords ? 'text' : 'password'" name="password_confirmation" 
                                           x-model="fields.password_confirmation" @input="validatePasswords"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 border"
                                           :class="errors.password_confirmation ? 'border-red-500' : 'border-gray-300'">
                                    <p x-show="errors.password_confirmation" class="text-red-500 text-xs mt-1" x-text="errors.password_confirmation"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center justify-end mt-10 gap-x-4">
                        <a href="{{ route('admin.professores.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Cancelar</a>
                        <button type="submit" 
                                :disabled="hasErrors"
                                class="rounded-md bg-indigo-600 px-8 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150">
                            Cadastrar Professor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function formValidation() {
        return {
            showPasswords: false,
            fields: {
                nome: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            errors: {
                nome: '',
                email: '',
                password_confirmation: ''
            },
            
            validateEmail() {
                const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                this.errors.email = re.test(this.fields.email) ? '' : 'Insira um e-mail válido.';
            },

            validatePasswords() {
                if (this.fields.password_confirmation && this.fields.password !== this.fields.password_confirmation) {
                    this.errors.password_confirmation = 'As senhas não coincidem.';
                } else {
                    this.errors.password_confirmation = '';
                }
            },

            get passwordStrengthWidth() {
                return Math.min((this.fields.password.length / 8) * 100, 100);
            },

            get passwordStrengthColor() {
                if (this.fields.password.length < 4) return 'bg-red-500';
                if (this.fields.password.length < 8) return 'bg-yellow-500';
                return 'bg-green-500';
            },

            get hasErrors() {
                return this.errors.email !== '' || 
                       this.errors.password_confirmation !== '' || 
                       this.fields.password.length < 8 ||
                       this.fields.nome.length < 3;
            },

            submitForm(e) {
                if (!this.hasErrors) {
                    e.target.submit();
                }
            }
        }
    }
</script>
@endsection