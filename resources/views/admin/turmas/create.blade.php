@extends('layouts.app')

@section('title', 'Cadastrar Turma')

@section('header')
<div class="flex items-center gap-4">
    <a href="{{ route('admin.turmas.index') }}" class="p-2.5 bg-white border border-gray-200 rounded-2xl text-gray-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="font-black text-2xl text-gray-800 tracking-tight leading-none">Nova Turma</h2>
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-500 mt-1">Cadastro de Unidade</p>
    </div>
</div>
@endsection

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 lg:px-8">
        <form method="POST" action="{{ route('admin.turmas.store') }}" class="space-y-8">
            @csrf

            <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 md:p-10 shadow-sm">
                <div class="space-y-8">
                    {{-- INPUTS PRINCIPAIS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nome da Turma</label>
                            <input type="text" name="nome" value="{{ old('nome') }}" required placeholder="Ex: Berçário 1"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-700 transition-all">
                            @error('nome') <p class="text-red-500 text-[10px] font-bold uppercase ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Ambiente / Sala</label>
                            <div class="relative">
                                <select name="sala_id" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 font-bold text-gray-700 appearance-none transition-all">
                                    <option value="">Selecione a Sala</option>
                                    @foreach($salas as $sala)
                                        <option value="{{ $sala->id }}" {{ old('sala_id') == $sala->id ? 'selected' : '' }}>{{ $sala->nome }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SELEÇÃO DE PROFESSORES --}}
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 block mb-4">Selecione os Docentes</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach ($professores as $prof)
                                @php $selected = in_array($prof->id, old('professores', [])); @endphp
                                <label class="flex items-center p-4 rounded-2xl border-2 transition-all cursor-pointer {{ $selected ? 'border-indigo-600 bg-indigo-50/30' : 'border-gray-50 bg-gray-50/50 hover:border-gray-200' }}">
                                    <input type="checkbox" name="professores[]" value="{{ $prof->id }}" {{ $selected ? 'checked' : '' }} 
                                           class="w-5 h-5 rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <div class="ml-4">
                                        <p class="text-xs font-black text-gray-700 uppercase tracking-tight">{{ $prof->nome }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase">Docente</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('professores') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 px-2">
                <a href="{{ route('admin.turmas.index') }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">
                    Cancelar Cadastro
                </a>
                <button type="submit" class="w-full sm:w-auto px-12 py-5 bg-gray-900 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl shadow-xl hover:bg-black hover:-translate-y-1 active:scale-95 transition-all">
                    Gravar Turma
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
</style>
@endsection