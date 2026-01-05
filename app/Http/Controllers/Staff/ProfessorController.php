<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        // 1. Validação
        $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Deve ser único na tabela users
            'password' => ['required', 'string', 'min:8'],
        ]);

        // 2. Criar a Conta de Usuário (Staff)
        $user = User::create([
            'name' => $request->nome, // Usa o nome do professor como nome de usuário
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff', // Define o perfil como 'staff'
        ]);

        // 3. Criar o Professor e vinculá-lo ao novo User
        Professor::create([
            'nome' => $request->nome,
            'cadastrado_por_user_id' => Auth::id(), // ID do Admin que cadastrou (conforme regra original)
            'user_id' => $user->id, // ID do novo Staff (linkando professor e usuário)
        ]);

        return redirect()->route('admin.professores.index')
                         ->with('success', 'Professor e conta de acesso Staff criados com sucesso!');
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