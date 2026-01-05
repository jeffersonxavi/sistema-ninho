<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sala;
use App\Models\Professor;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaController extends Controller
{
    
public function index()
{
    // O Admin vê TODAS as salas e TODAS as turmas
    $salas = Sala::with(['cadastradoPor', 'professores', 'turmas'])
                 ->orderBy('id', 'DESC')
                 ->get();

    $turmas = Turma::with(['professores', 'sala'])
                   ->orderBy('id', 'DESC')
                   ->get();

    // A view deve ser 'admin.salas.index' (ou 'admin.salas_turmas.index')
    return view('admin.salas.index', compact('salas', 'turmas'));
}

    /**
     * Mostra o formulário para criação de uma nova sala.
     */
    public function create()
    {
        // Necessário carregar todos os professores para a seleção múltipla.
        $professores = Professor::orderBy('nome')->get();
        return view('admin.salas.create', compact('professores'));
    }

    /**
     * Armazena uma nova sala e sincroniza os professores vinculados.
     */
    public function store(Request $request)
{
    $request->validate([
        'nome' => ['required', 'string', 'max:100', 'unique:salas,nome'],
        'professores' => ['nullable', 'array'],
        'professores.*' => ['exists:professores,id'],
    ]);

    $sala = Sala::create([
        'nome' => $request->nome,
        'cadastrado_por_user_id' => Auth::id(),
    ]);

    $professores = $request->input('professores', []);

    // 1) Vincula professores à sala
    $sala->professores()->sync($professores);

    // 2) Vincula automaticamente professores às turmas dessa sala
    foreach ($sala->turmas as $turma) {
        $turma->professores()->syncWithoutDetaching($professores);
    }

    return redirect()->route('admin.salas.index')
        ->with('success', 'Sala cadastrada e professores propagados para as turmas!');
}


    /**
     * Mostra o formulário para edição de uma sala existente.
     */
    public function edit(Sala $sala)
    {
        $professores = Professor::orderBy('nome')->get();
        // Carrega apenas os IDs dos professores atualmente vinculados para pré-seleção no formulário.
        $professoresVinculados = $sala->professores->pluck('id')->toArray(); 
        
        return view('admin.salas.edit', compact('sala', 'professores', 'professoresVinculados'));
    }

    /**
     * Atualiza uma sala e seus vínculos de professor.
     */
    public function update(Request $request, Sala $sala)
{
    $request->validate([
        'nome' => ['required', 'string', 'max:100', 'unique:salas,nome,' . $sala->id],
        'professores' => ['nullable', 'array'],
        'professores.*' => ['exists:professores,id'],
    ]);

    // Atualiza nome
    $sala->update(['nome' => $request->nome]);

    // Professores selecionados no formulário
    $novosProfessores = $request->input('professores', []);

    // Professores atualmente vinculados na sala
    $professoresAtuais = $sala->professores->pluck('id')->toArray();

    // Professores removidos
    $removidos = array_diff($professoresAtuais, $novosProfessores);

    // Professores adicionados
    $adicionados = array_diff($novosProfessores, $professoresAtuais);

    // 1️⃣ Sincroniza sala normalmente
    $sala->professores()->sync($novosProfessores);

    // 2️⃣ REMOVER professores das turmas desta sala
    if (!empty($removidos)) {
        foreach ($sala->turmas as $turma) {
            $turma->professores()->detach($removidos);
        }
    }

    // 3️⃣ ADICIONAR professores novos às turmas desta sala
    if (!empty($adicionados)) {
        foreach ($sala->turmas as $turma) {
            $turma->professores()->syncWithoutDetaching($adicionados);
        }
    }

    return redirect()->route('admin.salas.index')->with('success', 'Sala e vínculos atualizados com sucesso!');
}


    /**
     * Remove a sala do banco de dados.
     */
    public function destroy(Sala $sala)
    {
        // Nota: O onDelete('cascade') na migration professor_sala já limpa os vínculos.
        // Se a sala tiver turmas vinculadas, o delete pode falhar se não houver um onDelete('cascade') na FK da tabela turmas.
        $sala->delete();
        return redirect()->route('admin.salas.index')->with('success', 'Sala excluída com sucesso!');
    }
}