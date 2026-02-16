@extends('layouts.app')

@section('title', 'Gerenciamento de Professores')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciamento de Professores') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">Controle de acesso, finanças e datas importantes.</p>
        </div>
        <a href="{{ route('admin.professores.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition duration-150 shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Novo Professor
        </a>
    </div>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     class="mb-6 flex items-center p-4 text-sm text-green-800 border border-green-200 rounded-xl bg-green-50 shadow-sm" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <div><span class="font-bold">Sucesso!</span> {{ session('success') }}</div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80 font-bold text-gray-600">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Professor</th>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider italic">🎂 Aniversário</th>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider">Contato / CPF</th>
                                <th class="px-6 py-4 text-left text-xs uppercase tracking-wider italic">💰 Chave PIX</th>
                                <th class="px-6 py-4 text-right text-xs uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 font-medium">
                            @forelse ($professores as $professor)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    {{-- Professor --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-inner">
                                                {{ strtoupper(substr($professor->user->name ?? 'P', 0, 2)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">{{ $professor->user->name ?? 'Sem nome' }}</div>
                                                <div class="text-[10px] text-gray-400 font-mono tracking-tighter italic">ID: #{{ $professor->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- DATA DE ANIVERSÁRIO (Com destaque para o mês atual) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($professor->user->data_nascimento)
                                            @php
                                                $isBirthdayMonth = \Carbon\Carbon::parse($professor->user->data_nascimento)->month == now()->month;
                                            @endphp
                                            <span class="text-sm {{ $isBirthdayMonth ? 'bg-amber-100 text-amber-800 px-2 py-1 rounded-lg font-bold border border-amber-200' : 'text-gray-600' }}">
                                                {{ \Carbon\Carbon::parse($professor->user->data_nascimento)->format('d/m/Y') }}
                                                @if($isBirthdayMonth) 🥳 @endif
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-300 italic">Não informado</span>
                                        @endif
                                    </td>

                                    {{-- Contato / CPF --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs text-gray-700">{{ $professor->user->email ?? '—' }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $professor->user->cpf ?? '—' }}</div>
                                    </td>

                                    {{-- CHAVE PIX --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($professor->chave_pix)
                                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                {{ $professor->chave_pix }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-300 italic italic tracking-tight">Vazio</span>
                                        @endif
                                    </td>

                                    {{-- Ações --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.professores.edit', $professor) }}" 
                                               class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-all" title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </a>

                                            <form action="{{ route('admin.professores.destroy', $professor) }}" method="POST" 
                                                  onsubmit="return confirm('Tem certeza que deseja excluir?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-all" title="Excluir">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                        Nenhum professor registrado no sistema.
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