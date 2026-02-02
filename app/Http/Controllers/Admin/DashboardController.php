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

        // LÓGICA PARA STAFF / PROFESSOR
        if ($isStaff) {
            $professor = $user->professor;
            
            // Se o usuário é staff mas não tem registro na tabela professores
            if (!$professor) {
                return view('dashboard', [
                    'totalAlunos' => 0,
                    'totalTurmas' => 0,
                    'proximosAniversarios' => collect(),
                    'isStaff' => true
                ]);
            }

            $turmasIds = $professor->turmas()->pluck('turmas.id');
            
            $totalAlunos = Aluno::whereIn('turma_id', $turmasIds)->count();
            $totalTurmas = $turmasIds->count();
            
            // Aniversariantes do mês das turmas DESTE professor
            $proximosAniversarios = Aluno::whereIn('turma_id', $turmasIds)
                ->whereMonth('data_nascimento', Carbon::now()->month)
                ->orderByRaw('EXTRACT(day FROM data_nascimento) ASC')
                ->take(5)->get();
            
            return view('dashboard', compact('totalAlunos', 'totalTurmas', 'proximosAniversarios', 'isStaff'));
        }

        // LÓGICA PARA ADMIN
        $totalAlunos = Aluno::count();
        $totalProfessores = Professor::count();
        
        // Soma do valor_pago na tabela pagamentos
        $faturamentoMes = Pagamento::whereMonth('data_pagamento', Carbon::now()->month)
            ->where('status', 'Pago')
            ->sum('valor_pago');
        
        // Pagamentos vencidos (data_vencimento < hoje e status Pendente)
        $pendentes = Pagamento::where('status', 'Pendente')
            ->where('data_vencimento', '<', Carbon::now())
            ->count();

        // Alunos matriculados recentemente
$recentes = Aluno::with('turma.sala')->latest()->take(5)->get();
        return view('dashboard', compact(
            'totalAlunos', 
            'totalProfessores', 
            'faturamentoMes', 
            'pendentes', 
            'recentes', 
            'isStaff'
        ));
    }
}