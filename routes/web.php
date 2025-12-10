<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\TurmaController;
use App\Http\Middleware\IsAdmin;           // IMPORTADO
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rotas que requerem autenticação
Route::middleware(['auth', 'verified'])->group(function () {

    // Rota padrão do Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // ------------------------------------------------------------------
    // ROTAS ADMINISTRATIVAS (APENAS ADMIN)
    // Usamos a classe IsAdmin::class diretamente
    // ------------------------------------------------------------------
    Route::middleware([IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        
        // CRUD Professores
    Route::resource('professores', ProfessorController::class)->parameters([
        'professores' => 'professor'
    ]);

    Route::resource('salas', SalaController::class)->parameters([
        'salas' => 'sala'
    ]);

    Route::resource('turmas', TurmaController::class)->parameters([
        'turmas' => 'turma'
    ]);
        // TODO: Adicionar Rotas para Salas e Turmas aqui

    });

    // ------------------------------------------------------------------
    // Rotas de Perfil (ProfileController)
    // ------------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';