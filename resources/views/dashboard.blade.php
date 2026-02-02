<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Dashboard Principal') }}
            </h2>
            <span class="px-3 py-1 text-xs font-semibold text-indigo-600 bg-indigo-100 rounded-full uppercase tracking-wider">
                {{ Auth::user()->role }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                
                <div class="relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 overflow-hidden group">
                    <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative flex items-center">
                        <div class="p-3 bg-indigo-600 rounded-xl text-white shadow-lg shadow-indigo-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Alunos</p>
                            <p class="text-3xl font-extrabold text-gray-800">{{ $totalAlunos }}</p>
                        </div>
                    </div>
                </div>

                @if(!$isStaff)
                    <div class="relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 overflow-hidden group">
                        <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative flex items-center">
                            <div class="p-3 bg-emerald-500 rounded-xl text-white shadow-lg shadow-emerald-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Faturado (Mês)</p>
                                <p class="text-2xl font-extrabold text-gray-800">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 overflow-hidden group">
                        <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative flex items-center">
                            <div class="p-3 bg-rose-500 rounded-xl text-white shadow-lg shadow-rose-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Atrasados</p>
                                <p class="text-3xl font-extrabold text-gray-800">{{ $pendentes }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300 overflow-hidden group">
                        <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative flex items-center">
                            <div class="p-3 bg-amber-500 rounded-xl text-white shadow-lg shadow-amber-200">
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
                
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-white">
                        <h3 class="font-bold text-gray-800 text-lg flex items-center">
                            @if($isStaff)
                                <span class="mr-2">🎉</span> Aniversariantes do Mês
                            @else
                                <span class="mr-2">👤</span> Alunos Recém Matriculados
                            @endif
                        </h3>
                        <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">Atualizado hoje</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nome do Aluno</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $isStaff ? 'Data' : 'Turma/Sala' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($isStaff ? $proximosAniversarios : $recentes as $item)
                                    <tr class="hover:bg-indigo-50/30 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold mr-3">
                                                    {{ substr($item->nome_completo, 0, 1) }}
                                                </div>
                                                <span class="text-sm font-semibold text-gray-700">{{ $item->nome_completo }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($isStaff)
                                                <span class="px-2.5 py-1 rounded-md bg-pink-50 text-pink-600 text-xs font-bold">
                                                    {{ $item->data_nascimento->format('d/m') }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500 font-medium">
                                                    {{ $item->turma->nome ?? 'N/A' }} 
                                                    <span class="text-gray-300 mx-1">|</span> 
                                                    <span class="text-xs text-gray-400">{{ $item->turma->sala->nome ?? '' }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-10 text-center text-gray-400 italic">Nenhum registro encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5">
                            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-6">Ações Rápidas</h3>
                        <div class="space-y-3">
                            @if(!$isStaff)
                                <a href="{{ route('admin.professores.index') }}" class="group flex items-center p-3 bg-indigo-50/50 rounded-xl hover:bg-indigo-600 transition-all duration-300">
                                    <div class="p-2 bg-white rounded-lg shadow-sm group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <span class="ml-3 font-bold text-indigo-700 group-hover:text-white">Professores</span>
                                </a>
                                <a href="{{ route('admin.pagamentos.index') }}" class="group flex items-center p-3 bg-emerald-50/50 rounded-xl hover:bg-emerald-600 transition-all duration-300">
                                    <div class="p-2 bg-white rounded-lg shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <span class="ml-3 font-bold text-emerald-700 group-hover:text-white">Financeiro</span>
                                </a>
                            @endif
                            <a href="{{ route('admin.salas.index') }}" class="group flex items-center p-3 bg-gray-100 rounded-xl hover:bg-gray-800 transition-all duration-300">
                                <div class="p-2 bg-white rounded-lg shadow-sm group-hover:bg-gray-700 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                </div>
                                <span class="ml-3 font-bold text-gray-700 group-hover:text-white">Salas & Turmas</span>
                            </a>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-6 rounded-2xl shadow-lg text-white">
                        <p class="text-indigo-100 text-xs font-bold uppercase tracking-widest mb-2">Suporte do Sistema</p>
                        <p class="text-sm font-medium mb-4">Precisa de ajuda com o gerenciamento de alunos?</p>
                        <button class="w-full bg-white/10 hover:bg-white/20 py-2 rounded-lg text-sm font-bold transition-colors border border-white/20">
                            Abrir Chamado
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>