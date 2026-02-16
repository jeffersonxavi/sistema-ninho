<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfessorController extends Controller
{
    public function index()
    {
        $professores = Professor::with(['user', 'cadastradoPor'])->latest()->get();
                //dd($professores);
        return view('admin.professores.index', compact('professores'));

    }

    public function create()
    {
        return view('admin.professores.create');
    }

    public function store(Request $request)
    {
        // 1. Validação (Sincronizada com os campos do Blade)
        $request->validate([
            'name'            => ['required', 'string', 'max:150'],
            'email'           => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
            'cpf'             => ['nullable', 'string', 'max:20'],
            'telefone'        => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'chave_pix'       => ['nullable', 'string', 'max:255'],
        ]);

        // 2. Criar a Conta de Usuário (Tabela users)
        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role'            => 'staff', 
            'cpf'             => $request->cpf,
            'telefone'        => $request->telefone,
            'data_nascimento' => $request->data_nascimento,
        ]);

        // 3. Criar o Professor vinculado (Tabela professores)
        Professor::create([
            'nome'                   => $request->name,
            'user_id'                => $user->id,
            'chave_pix'              => $request->chave_pix,
            'cadastrado_por_user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.professores.index')
                         ->with('success', 'Professor cadastrado com sucesso!');
    }

    public function edit(Professor $professor)
    {
        // Carrega o usuário para garantir que os dados estejam disponíveis no Blade
        $professor->load('user');
        return view('admin.professores.edit', compact('professor'));
    }

    public function update(Request $request, Professor $professor)
    {
        // 1. Validação na Edição
        $request->validate([
            'name'            => ['required', 'string', 'max:150'],
            'email'           => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($professor->user_id)],
            'password'        => ['nullable', 'string', 'min:8', 'confirmed'], // Senha opcional na edição
            'cpf'             => ['nullable', 'string', 'max:20'],
            'telefone'        => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'chave_pix'       => ['nullable', 'string', 'max:255'],
        ]);

        // 2. Atualizar Dados do Usuário
        $userData = [
            'name'            => $request->name,
            'email'           => $request->email,
            'cpf'             => $request->cpf,
            'telefone'        => $request->telefone,
            'data_nascimento' => $request->data_nascimento,
        ];

        // Só atualiza a senha se o usuário preencheu o campo
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $professor->user->update($userData);

        // 3. Atualizar Dados do Professor
        $professor->update([
            'chave_pix' => $request->chave_pix,
        ]);

        return redirect()->route('admin.professores.index')
                         ->with('success', 'Dados do professor atualizados!');
    }

    public function destroy(Professor $professor)
    {
        // Ao excluir o professor, você pode querer excluir o User também
        $user = $professor->user;
        
        $professor->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.professores.index')
                         ->with('success', 'Professor e conta de acesso removidos.');
    }
}