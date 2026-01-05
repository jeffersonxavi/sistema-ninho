<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Sala;
use App\Models\Professor;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaController extends Controller
{

public function index()
{
    // Professor vinculado ao usuário logado
    $professor = Auth::user()->professor;

    // Se o usuário não é professor → lista vazia
    if (!$professor) {
        return view('staff.salas.index', [
            'salas'  => collect(),
            'turmas' => collect(),
        ]);
    }

    // Filtro padrão para garantir que só carregue dados do professor logado
    $somenteProfessorLogado = fn($q) =>
        $q->where('professor_id', $professor->id);

    /**
     * TURMAS que pertencem a ESTE professor
     */
    $turmas = $professor->turmas()
        ->with([
            'sala',
            'cadastradoPor',
            'professores' => $somenteProfessorLogado,     // só ele
        ])
        ->latest()
        ->get();

    /**
     * SALAS vinculadas ao professor:
     * - pela própria sala OU
     * - por turmas onde ele é professor
     */
    $salas = Sala::query()
        ->whereHas('professores', $somenteProfessorLogado)
        ->orWhereHas('turmas.professores', $somenteProfessorLogado)
        ->with([
            'cadastradoPor',
            'turmas',
            'professores' => $somenteProfessorLogado,     // só ele
            'turmas.professores' => $somenteProfessorLogado,
        ])
        ->latest()
        ->get();

    return view('staff.salas.index', compact('salas', 'turmas'));
}
public function alunos(Turma $turma)
{
    // Carrega os alunos daquela turma específica
    $turma->load('alunos'); 
    
    return view('staff.salas.alunos', compact('turma'));
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
