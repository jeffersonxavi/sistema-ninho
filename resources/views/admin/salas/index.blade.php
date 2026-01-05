@extends('layouts.app')
@section('title', 'Salas')
@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Gerenciamento de Salas e Turmas') }}
</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
                @endif

                <div x-data="{ aba: '{{ request()->is('*turmas*') ? 'turmas' : 'salas' }}' }">
                    <div class="flex gap-8 border-b pb-2 mb-6">
                        <button
                            class="px-4 py-2 text-sm font-semibold transition-colors"
                            :class="aba === 'salas' ? 'border-b-3 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                            @click="aba = 'salas'">
                            Salas
                        </button>
                        <button
                            class="px-4 py-2 text-sm font-semibold transition-colors"
                            :class="aba === 'turmas' ? 'border-b-3 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'"
                            @click="aba = 'turmas'">
                            Turmas
                        </button>
                    </div>

                    {{-- Função para gerar cor consistente e legível --}}
                    @php
                        function entityColor($prefix, $id) {
                            $hash = md5($prefix . $id);
                            $r = hexdec(substr($hash, 0, 2));
                            $g = hexdec(substr($hash, 2, 2));
                            $b = hexdec(substr($hash, 4, 2));
                            // Tons médios para boa legibilidade com texto branco
                            $r = max(80, min(200, ($r % 120) + 100));
                            $g = max(80, min(200, ($g % 120) + 100));
                            $b = max(80, min(200, ($b % 120) + 100));
                            return "rgb($r,$g,$b)";
                        }
                    @endphp

                    {{-- =================================== ABA SALAS =================================== --}}
                    <div x-show="aba === 'salas'">
                        <div class="mb-6 flex justify-between items-center">
                            <h3 class="text-lg font-bold">Lista de Salas</h3>
                            <a href="{{ route('admin.salas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">
                                + Nova Sala
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sala</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professores e Turmas Lecionadas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turmas na Sala</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado Por</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($salas as $sala)
                                    @php $salaColor = entityColor('sala', $sala->id); @endphp
                                    <tr>
                                        <td class="px-6 py-4">
                                            <span class="inline-block px-3 py-1.5 text-sm font-bold text-white rounded-md shadow-sm"
                                                  style="background-color: {{ $salaColor }};">
                                                {{ $sala->nome }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="space-y-2">
                                                @forelse ($sala->professores as $professor)
                                                @php $profColor = entityColor('prof', $professor->id); @endphp
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-white rounded"
                                                          style="background-color: {{ $profColor }};">
                                                        {{ $professor->nome }}
                                                    </span>
                                                    <span class="text-xs text-gray-600">→ leciona nas turmas:</span>
                                                    @php
                                                        // Turmas que este professor dá nesta sala
                                                        $turmasDoProfNaSala = $sala->turmas->filter(function($turma) use ($professor) {
                                                            return $turma->professores->contains($professor);
                                                        });
                                                    @endphp
                                                    @if($turmasDoProfNaSala->isNotEmpty())
                                                        @foreach($turmasDoProfNaSala as $turma)
                                                        @php $turmaColor = entityColor('turma', $turma->id); @endphp
                                                        <span class="inline-block px-2 py-0.5 text-xs font-medium text-white rounded"
                                                              style="background-color: {{ $turmaColor }};">
                                                            {{ $turma->nome }}
                                                        </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-xs text-gray-400 italic">nenhuma turma nesta sala</span>
                                                    @endif
                                                </div>
                                                @empty
                                                <span class="text-xs text-gray-500">Nenhum professor vinculado</span>
                                                @endforelse
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse ($sala->turmas as $turma)
                                                @php $turmaColor = entityColor('turma', $turma->id); @endphp
                                                <span class="inline-block px-2.5 py-1 text-xs font-medium text-white rounded"
                                                      style="background-color: {{ $turmaColor }};">
                                                    {{ $turma->nome }}
                                                </span>
                                                @empty
                                                <span class="text-xs text-gray-500">Nenhuma turma</span>
                                                @endforelse
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $sala->cadastradoPor->name ?? '—' }}
                                        </td>

                                        <td class="px-6 py-4 text-center space-x-4">
                                            <a href="{{ route('admin.salas.edit', $sala) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Editar</a>
                                            <form action="{{ route('admin.salas.destroy', $sala) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Confirmar exclusão? Esta sala pode estar vinculada a turmas.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- =================================== ABA TURMAS =================================== --}}
                    <div x-show="aba === 'turmas'">
                        <div class="mb-6 flex justify-between items-center">
                            <h3 class="text-lg font-bold">Lista de Turmas</h3>
                            <a href="{{ route('admin.turmas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">
                                + Nova Turma
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turma</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professores e Sala</th>
                                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sala</th> -->
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alunos</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($turmas as $turma)
                                    @php
                                        $turmaColor = entityColor('turma', $turma->id);
                                        $salaColor = $turma->sala ? entityColor('sala', $turma->sala->id) : null;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4">
                                            <span class="inline-block px-3 py-1.5 text-sm font-bold text-white rounded-md shadow-sm"
                                                  style="background-color: {{ $turmaColor }};">
                                                {{ $turma->nome }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="space-y-2">
                                                @forelse ($turma->professores as $professor)
                                                @php $profColor = entityColor('prof', $professor->id); @endphp
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-white rounded"
                                                          style="background-color: {{ $profColor }};">
                                                        {{ $professor->nome }}
                                                    </span>
                                                    @if($turma->sala)
                                                    <span class="text-xs text-gray-600">→ leciona nesta turma na sala:</span>
                                                    <span class="inline-block px-3 py-1 text-xs font-medium text-white rounded"
                                                          style="background-color: {{ $salaColor }};">
                                                        {{ $turma->sala->nome }}
                                                    </span>
                                                    @endif
                                                </div>
                                                @empty
                                                <span class="text-xs text-gray-500">Nenhum professor</span>
                                                @endforelse
                                            </div>
                                        </td>

                                        <!-- <td class="px-6 py-4">
                                            @if($turma->sala)
                                            <span class="inline-block px-3 py-1.5 text-sm font-medium text-white rounded shadow-sm"
                                                  style="background-color: {{ $salaColor }};">
                                                {{ $turma->sala->nome }}
                                            </span>
                                            @else
                                            <span class="text-xs text-gray-500">—</span>
                                            @endif
                                        </td> -->

                                        <td class="px-6 py-4">
                                            <div>
                                                <span class="text-sm font-bold text-gray-700">{{ $turma->alunos->count() }} Alunos</span><br>
                                                <span class="text-xs text-gray-500 truncate block max-w-xs">
                                                    {{ $turma->alunos->pluck('nome_completo')->implode(', ') ?: 'Nenhum aluno matriculado' }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-center space-y-2">
                                            <a href="{{ route('admin.turmas.show', $turma) }}"
                                               class="block text-green-600 hover:text-green-800 font-semibold">
                                                Visualizar
                                            </a>
                                            <div class="flex justify-center gap-4">
                                                <a href="{{ route('admin.turmas.edit', $turma) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                                <form action="{{ route('admin.turmas.destroy', $turma) }}" method="POST"
                                                      onsubmit="return confirm('Excluir turma?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection