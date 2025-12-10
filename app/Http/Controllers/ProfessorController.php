<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorController extends Controller
{
    /**
     * Display a listing of the resource.
     * Lista todos os professores
     */
    public function index()
    {
        $professores = Professor::with('cadastradoPor')->latest()->get();
        // Retorna a view para listar os professores
        return view('admin.professores.index', compact('professores'));
    }

    /**
     * Show the form for creating a new resource.
     * Exibe o formulário de criação
     */
    public function create()
    {
        return view('admin.professores.create');
    }

    /**
     * Store a newly created resource in storage.
     * Salva o novo professor no banco de dados
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:150'],
        ]);

        Professor::create([
            'nome' => $request->nome,
            'cadastrado_por_user_id' => Auth::id(), // Registra o Admin logado
        ]);

        return redirect()->route('admin.professores.index')
                         ->with('success', 'Professor cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Professor $professor)
    {
        return view('admin.professores.edit', compact('professor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Professor $professor)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:150'],
        ]);

        $professor->update([
            'nome' => $request->nome,
        ]);

        return redirect()->route('admin.professores.index')
                         ->with('success', 'Professor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Professor $professor)
    {
        $professor->delete();

        return redirect()->route('admin.professores.index')
                         ->with('success', 'Professor excluído com sucesso!');
    }
    
    // Os métodos show() não são estritamente necessários, mas podem ser implementados se desejar.
}