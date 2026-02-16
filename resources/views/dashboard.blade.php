<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard Principal') }}
                </h2>
                <p class="text-sm text-gray-500">Bem-vindo de volta, {{ Auth::user()->name }}</p>
            </div>
            <span class="px-4 py-1.5 text-xs font-bold text-indigo-700 bg-indigo-100 rounded-full uppercase tracking-widest border border-indigo-200">
                🚀 {{ Auth::user()->role }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 1. Cards de Estatísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 group transition-all hover:scale-[1.02]">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-600 rounded-xl text-white shadow-lg shadow-indigo-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Alunos</p>
                            <p class="text-3xl font-extrabold text-gray-800">{{ $totalAlunos }}</p>
                        </div>
                    </div>
                </div>

                @if(!$isStaff)
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:scale-[1.02]">
                        <div class="flex items-center">
                            <div class="p-3 bg-emerald-500 rounded-xl text-white shadow-lg shadow-emerald-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Faturado (Mês)</p>
                                <p class="text-2xl font-extrabold text-gray-800">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:scale-[1.02]">
                        <div class="flex items-center">
                            <div class="p-3 bg-rose-500 rounded-xl text-white shadow-lg shadow-rose-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Atrasados</p>
                                <p class="text-3xl font-extrabold text-gray-800">{{ $pendentes }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 transition-all hover:scale-[1.02]">
                        <div class="flex items-center">
                            <div class="p-3 bg-amber-500 rounded-xl text-white shadow-lg shadow-amber-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Minhas Turmas</p>
                                <p class="text-3xl font-extrabold text-gray-800">{{ $totalTurmas }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- 2. Coluna da Esquerda: Aniversariantes --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- Aniversariantes Alunos (Sempre Visível) --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-white to-indigo-50/30">
                            <h3 class="font-bold text-gray-800 text-lg flex items-center">
                                <span class="mr-3 p-2 bg-indigo-100 rounded-lg text-indigo-600">🎓</span> 
                                Aniversariantes Alunos ({{ now()->locale('pt_BR')->monthName }})
                            </h3>
                        </div>
                        <div class="{{ $isStaff ? '' : 'max-h-[300px]' }} overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($aniversariantesAlunos as $aluno)
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold mr-3 border border-indigo-200 uppercase">
                                                        {{ substr($aluno->nome_completo, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <span class="text-sm font-bold text-gray-800 block leading-tight">{{ $aluno->nome_completo }}</span>
                                                        <span class="text-[10px] text-gray-400 uppercase font-semibold">{{ $aluno->turma->nome ?? 'S/ Turma' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-black">
                                                    {{ \Carbon\Carbon::parse($aluno->data_nascimento)->format('d/m') }} 🎂
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td class="px-6 py-8 text-center text-gray-400 italic">Nenhum aluno aniversariante este mês.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Aniversariantes Staff (Somente Admin vê) --}}
                    @if(!$isStaff)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-white to-emerald-50/30">
                                <h3 class="font-bold text-gray-800 text-lg flex items-center">
                                    <span class="mr-3 p-2 bg-emerald-100 rounded-lg text-emerald-600">💼</span> 
                                    Aniversariantes Staff
                                </h3>
                            </div>
                            <div class="max-h-[300px] overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse($aniversariantesStaff as $staff)
                                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="h-9 w-9 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold mr-3 shadow-sm uppercase">
                                                            {{ substr($staff->user->name ?? 'S', 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <span class="text-sm font-bold text-gray-800 block">{{ $staff->user->name ?? 'Sem Nome' }}</span>
                                                            <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-tighter">Docente / Administrativo</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black">
                                                        {{-- Pegando a data da tabela Users através do relacionamento --}}
                                                        {{ \Carbon\Carbon::parse($staff->user->data_nascimento)->format('d/m') }} ✨
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td class="px-6 py-8 text-center text-gray-400 italic">Ninguém da equipe faz aniversário este mês.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 3. Coluna da Direita: Ações e Suporte --}}
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Acesso Rápido
                        </h3>
                        <div class="space-y-3">
                            @if(!$isStaff)
                                <a href="{{ route('admin.professores.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:bg-indigo-600 hover:text-white group transition-all duration-200">
                                    <div class="p-2 bg-indigo-50 rounded-lg group-hover:bg-indigo-500 transition-colors text-indigo-600 group-hover:text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <span class="ml-3 font-bold text-sm">Gerenciar Professores</span>
                                </a>
                                <a href="{{ route('admin.pagamentos.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:bg-emerald-600 hover:text-white group transition-all duration-200">
                                    <div class="p-2 bg-emerald-50 rounded-lg group-hover:bg-emerald-500 transition-colors text-emerald-600 group-hover:text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <span class="ml-3 font-bold text-sm">Controle Financeiro</span>
                                </a>
                            @endif
                            <a href="{{ route('admin.salas.index') }}" class="flex items-center p-3 rounded-xl border border-gray-100 hover:bg-gray-800 hover:text-white group transition-all duration-200">
                                <div class="p-2 bg-gray-50 rounded-lg group-hover:bg-gray-700 transition-colors text-gray-600 group-hover:text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                </div>
                                <span class="ml-3 font-bold text-sm">Salas & Turmas</span>
                            </a>
                        </div>
                    </div>

                    {{-- <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-6 rounded-2xl shadow-lg text-white relative overflow-hidden">
                        <div class="relative z-10">
                            <p class="text-indigo-100 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Central de Ajuda</p>
                            <p class="text-sm font-medium mb-4">Dúvidas sobre o fechamento de turmas ou novos cadastros?</p>
                            <button class="w-full bg-white text-indigo-700 py-2.5 rounded-xl text-xs font-black uppercase shadow-md hover:bg-indigo-50 transition-colors">
                                Falar com Suporte
                            </button>
                        </div>
                        <svg class="absolute bottom-0 right-0 w-24 h-24 -mb-8 -mr-8 text-white/10" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </div> --}}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>