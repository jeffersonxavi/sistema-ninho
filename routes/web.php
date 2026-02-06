<?php

use App\Http\Controllers\Admin\AlunoController as AdminAlunoController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PagamentoController as AdminPagamentoController;
use App\Http\Controllers\Admin\ProfessorController as AdminProfessorController;
use App\Http\Controllers\Admin\SalaController as AdminSalaController;
use App\Http\Controllers\Admin\TurmaController as AdminTurmaController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\Staff\AlunoController as StaffAlunoController;
use App\Http\Controllers\Staff\PagamentoController;
use App\Http\Controllers\Staff\SalaController as StaffSalaController;
use App\Http\Controllers\Staff\TurmaController as StaffTurmaController;
use App\Http\Controllers\TurmaController;
use App\Http\Middleware\IsStaff;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard padrão
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // ================================================================
    // OPCIONAL: Mantém o painel /admin/... só para administradores
    // (útil se quiser separar visualmente ou manter histórico)
    // ================================================================
    Route::middleware(\App\Http\Middleware\IsAdmin::class)
        ->prefix('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::resource('professores', AdminProfessorController::class)
                ->parameters(['professores' => 'professor']);

            Route::resource('salas', AdminSalaController::class)
                ->parameters(['salas' => 'sala']);

            Route::resource('turmas', AdminTurmaController::class)
                ->parameters(['turmas' => 'turma']);

            Route::resource('alunos', AdminAlunoController::class)
                ->parameters(['alunos' => 'aluno']);

            Route::resource('pagamentos', AdminPagamentoController::class)
                ->parameters(['pagamentos' => 'pagamento']);
            // Rota específica para gerar/download do Contrato PDF
            Route::get('alunos/{aluno}/contrato/download', [App\Http\Controllers\Admin\AlunoController::class, 'downloadContrato'])->name('alunos.contrato.download');
        });
    // ================================================================
    // TODAS AS ROTAS DE GESTÃO (Professores, Salas, Turmas, Alunos, Pagamentos)
    // Disponíveis para ADMIN e STAFF
    // URLs limpas: /alunos, /professores, /pagamentos etc.
    // ================================================================
    Route::middleware(IsStaff::class)
        ->as('staff.')
        ->group(function () {

            // Adicione esta linha para as turmas
        Route::resource('turmas', StaffSalaController::class);
        
        // E esta linha específica para a lista de alunos de uma turma
        Route::get('turmas/{turma}/alunos', [StaffSalaController::class, 'alunos'])->name('turmas.alunos');
            Route::resource('salas', StaffSalaController::class)
                ->parameters(['salas' => 'sala']);

            Route::resource('alunos', StaffAlunoController::class)
                ->parameters(['alunos' => 'aluno']);

            Route::resource('pagamentos', PagamentoController::class)
                ->parameters(['pagamentos' => 'pagamento']);

            Route::get(
                'alunos/{aluno}/contrato/download',
                [StaffAlunoController::class, 'downloadContrato']
            )->name('alunos.contrato.download');
            
        });


    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
