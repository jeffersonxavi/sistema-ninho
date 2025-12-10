<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfessorController; // Importar o Controller
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rotas que requerem autenticação
Route::middleware(['auth', 'verified'])->group(function () {

    // Rota padrão do Dashboard
    Route::get('/dashboard', function () {
        // Redirecionamento condicional pode ser adicionado aqui depois (Admin -> Painel Admin, Staff -> Painel Staff)
        return view('dashboard');
    })->name('dashboard');
    
    // ------------------------------------------------------------------
    // ROTAS ADMINISTRATIVAS (APENAS ADMIN)
    // Aplica o middleware 'admin' na URI '/admin'
    // ------------------------------------------------------------------
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        
        // CRUD Professores
        Route::resource('professores', ProfessorController::class);

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