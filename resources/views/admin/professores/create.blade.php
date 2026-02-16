@extends('layouts.app')

@section('title', isset($professor) ? 'Editar Professor' : 'Cadastrar Professor')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ isset($professor) ? 'Editar Professor' : 'Cadastrar Novo Professor' }}
    </h2>
@endsection

@section('content')
<div class="py-12"
     x-data="formValidation(
        {{ isset($professor) ? json_encode($professor->user) : '{}' }},
        {{ isset($professor) ? json_encode($professor) : '{}' }}
     )">

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
            <div class="p-8">

                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{ isset($professor) ? 'Editar Dados do Professor' : 'Detalhes do Novo Professor' }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        Campos marcados com <span class="text-red-500">*</span> são obrigatórios.
                    </p>
                </div>

                <form method="POST"
                      action="{{ isset($professor) ? route('admin.professores.update', $professor) : route('admin.professores.store') }}"
                      @submit.prevent="submitForm">
                    @csrf
                    @if(isset($professor))
                        @method('PUT')
                    @endif

                    <div class="space-y-8">

                        {{-- ===================== --}}
                        {{-- DADOS PESSOAIS        --}}
                        {{-- ===================== --}}
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <h4 class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-6">
                                Dados Pessoais

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nome --}}
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Nome Completo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="fields.name" required
                                           class="mt-1 block w-full rounded-md shadow-sm sm:text-sm p-2.5 border"
                                           :class="errors.name || {{ $errors->has('name') ? 'true' : 'false' }} ? 'border-red-500' : 'border-gray-300'">
                                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- CPF --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">CPF</label>
                                    <input type="text" name="cpf" x-model="fields.cpf" placeholder="000.000.000-00"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2.5 border">
                                    @error('cpf') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Telefone --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Telefone</label>
                                    <input type="text" name="telefone" x-model="fields.telefone" placeholder="(00) 00000-0000"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2.5 border">
                                    @error('telefone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Data de Nascimento --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Data de Nascimento</label>
                                    <input type="date" name="data_nascimento" x-model="fields.data_nascimento"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2.5 border">
                                    @error('data_nascimento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- ===================== --}}
                        {{-- CREDENCIAIS           --}}
                        {{-- ===================== --}}
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <h4 class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-6">
                                Credenciais de Acesso
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Email --}}
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Email Institucional <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" x-model="fields.email" @blur="validateEmail" required
                                           class="mt-1 block w-full rounded-md shadow-sm sm:text-sm p-2.5 border"
                                           :class="errors.email || {{ $errors->has('email') ? 'true' : 'false' }} ? 'border-red-500' : 'border-gray-300'">
                                    <p x-show="errors.email" class="text-red-500 text-xs mt-1" x-text="errors.email"></p>
                                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Senha --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Senha 
                                        <span class="text-xs text-gray-400" x-text="isEditForm ? '(deixe em branco para manter)' : '(mínimo 8 caracteres)'"></span>
                                    </label>
                                    <input type="password" name="password" x-model="fields.password" @input="validatePasswords"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2.5 border">
                                    
                                    <div class="mt-2 h-1 w-full bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full transition-all duration-500"
                                             :class="passwordStrengthColor"
                                             :style="`width: ${passwordStrengthWidth}%`">
                                        </div>
                                    </div>
                                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Confirmar Senha --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                                    <input type="password" name="password_confirmation" x-model="fields.password_confirmation" @input="validatePasswords"
                                           class="mt-1 block w-full rounded-md shadow-sm sm:text-sm p-2.5 border"
                                           :class="errors.password_confirmation ? 'border-red-500' : 'border-gray-300'">
                                    <p x-show="errors.password_confirmation" class="text-red-500 text-xs mt-1" x-text="errors.password_confirmation"></p>
                                </div>
                            </div>
                        </div>

                        {{-- ===================== --}}
                        {{-- DADOS FINANCEIROS     --}}
                        {{-- ===================== --}}
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                            <h4 class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-6">
                                Dados Financeiros
                            </h4>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Chave PIX</label>
                                <input type="text" name="chave_pix" x-model="fields.chave_pix" placeholder="CPF, Email, Telefone ou Aleatória"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2.5 border">
                                @error('chave_pix') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- BOTÕES --}}
                        <div class="flex items-center justify-end mt-10 gap-x-4">
                            <a href="{{ route('admin.professores.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                            <button type="submit" :disabled="hasErrors"
                                    class="rounded-md bg-indigo-600 px-8 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150">
                                {{ isset($professor) ? 'Atualizar Professor' : 'Cadastrar Professor' }}
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
function formValidation(existingUser = {}, existingProfessor = {}) {
    return {
        // Verifica se é edição baseando-se na existência de um ID de usuário
        isEditForm: !!(existingUser && existingUser.id),

        fields: {
            name: existingUser.name || '',
            email: existingUser.email || '',
            cpf: existingUser.cpf || '',
            telefone: existingUser.telefone || '',
            // Garante formato YYYY-MM-DD para o input date
            data_nascimento: existingUser.data_nascimento ? existingUser.data_nascimento.split('T')[0] : '',
            chave_pix: existingProfessor.chave_pix || '',
            password: '',
            password_confirmation: ''
        },

        errors: {
            name: '',
            email: '',
            password_confirmation: ''
        },

        validateEmail() {
            if (!this.fields.email) return;
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            this.errors.email = re.test(this.fields.email) ? '' : 'Insira um e-mail válido.';
        },

        validatePasswords() {
            this.errors.password_confirmation = '';
            if (this.fields.password_confirmation && this.fields.password !== this.fields.password_confirmation) {
                this.errors.password_confirmation = 'As senhas não coincidem.';
            }
        },

        get passwordStrengthWidth() {
            if (!this.fields.password) return 0;
            return Math.min((this.fields.password.length / 8) * 100, 100);
        },

        get passwordStrengthColor() {
            if (this.fields.password.length < 4) return 'bg-red-500';
            if (this.fields.password.length < 8) return 'bg-yellow-500';
            return 'bg-green-500';
        },

        get hasErrors() {
            // Se for novo, senha > 8 é obrigatória. Se for edição, só valida se digitar algo.
            const passLength = this.fields.password.length;
            const isPasswordInvalid = !this.isEditForm ? passLength < 8 : (passLength > 0 && passLength < 8);

            return (
                !this.fields.name || 
                this.fields.name.length < 3 || 
                !this.fields.email || 
                !!this.errors.email || 
                isPasswordInvalid || 
                !!this.errors.password_confirmation
            );
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