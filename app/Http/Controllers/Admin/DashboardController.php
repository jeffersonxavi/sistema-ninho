<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Professor;
use App\Models\Pagamento;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isStaff = $user->role === 'staff';
        $mesAtual = Carbon::now()->month;

        // --- LÓGICA COMPARTILHADA: Aniversariantes Staff ---
        // Usamos join para acessar a data_nascimento na tabela users
        $aniversariantesStaff = Professor::join('users', 'professores.user_id', '=', 'users.id')
            ->select('professores.*', 'users.data_nascimento as dt_nasc') // Alias para facilitar
            ->whereMonth('users.data_nascimento', $mesAtual)
            ->orderByRaw('EXTRACT(DAY FROM users.data_nascimento) ASC')
            ->with('user')
            ->get();

        // --- LÓGICA PARA STAFF / PROFESSOR ---
        if ($isStaff) {
            $professor = $user->professor;
            
            if (!$professor) {
                return view('dashboard', [
                    'totalAlunos' => 0,
                    'totalTurmas' => 0,
                    'aniversariantesAlunos' => collect(),
                    'aniversariantesStaff' => $aniversariantesStaff,
                    'isStaff' => true
                ]);
            }

            $turmasIds = $professor->turmas()->pluck('turmas.id');
            $totalAlunos = Aluno::whereIn('turma_id', $turmasIds)->count();
            $totalTurmas = $turmasIds->count();
            
            // Se Alunos também usarem a tabela Users, precisa de join aqui também.
            // Se a data estiver na tabela alunos, mantemos assim:
            $aniversariantesAlunos = Aluno::whereIn('turma_id', $turmasIds)
                ->whereMonth('data_nascimento', $mesAtual)
                ->orderByRaw('EXTRACT(DAY FROM data_nascimento) ASC')
                ->get();
            
            return view('dashboard', compact('totalAlunos', 'totalTurmas', 'aniversariantesAlunos', 'aniversariantesStaff', 'isStaff'));
        }

        // --- LÓGICA PARA ADMIN ---
        $totalAlunos = Aluno::count();
        $totalProfessores = Professor::count();
        
        $faturamentoMes = Pagamento::whereMonth('data_pagamento', $mesAtual)
            ->where('status', 'Pago')
            ->sum('valor_pago');
        
        $pendentes = Pagamento::where('status', 'Pendente')
            ->where('data_vencimento', '<', Carbon::now())
            ->count();

        // Admin vê todos os alunos (PostgreSQL syntax)
        $aniversariantesAlunos = Aluno::with('turma')
            ->whereMonth('data_nascimento', $mesAtual)
            ->orderByRaw('EXTRACT(DAY FROM data_nascimento) ASC')
            ->get();

        return view('dashboard', compact(
            'totalAlunos', 
            'totalProfessores', 
            'faturamentoMes', 
            'pendentes', 
            'aniversariantesAlunos', 
            'aniversariantesStaff', 
            'isStaff'
        ));
    }
}