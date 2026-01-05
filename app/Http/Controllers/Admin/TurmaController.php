<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Turma;
use App\Models\Professor;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurmaController extends Controller
{
    /*    public function index()
    {
        // Carrega turmas e seus relacionamentos: N:M com professores e N:1 com sala.
        $turmas = Turma::with(['professores', 'sala', 'cadastradoPor'])->latest()->get();
        return view('admin.turmas.index', compact('turmas'));
    }
 */

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


    public function show(Turma $turma)
    {
        // Carrega os alunos ordenados por nome e as informações da sala/professores
        $turma->load([
            'sala',
            'professores',
            'alunos' => function ($query) {
                $query->orderBy('nome_completo', 'asc');
            }
        ]);

        return view('admin.turmas.show', compact('turma'));
    }
    public function create()
    {
        $professores = Professor::orderBy('nome')->get();
        $salas = Sala::orderBy('nome')->get();
        return view('admin.turmas.create', compact('professores', 'salas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'professores' => ['required', 'array'],
            'professores.*' => ['exists:professores,id'],
            'sala_id' => ['required', 'exists:salas,id'],
        ]);

        $turma = Turma::create([
            'nome' => $request->nome,
            'sala_id' => $request->sala_id,
            'cadastrado_por_user_id' => Auth::id(),
        ]);

        // ➕ Vincula professores à turma (pivot turma_professor)
        $turma->professores()->sync($request->professores);

        // ➕ Vincula professores à sala automaticamente (pivot professor_sala)
        $sala = Sala::find($request->sala_id);
        $sala->professores()->syncWithoutDetaching($request->professores);

        return redirect()->route('admin.turmas.index')
            ->with('success', 'Turma criada e professores vinculados!');
    }

    public function edit(Turma $turma)
    {
        $professores = Professor::orderBy('nome')->get();
        $salas = Sala::orderBy('nome')->get();

        // Carrega IDs dos professores já vinculados
        $professoresVinculados = $turma->professores->pluck('id')->toArray();

        return view('admin.turmas.edit', compact('turma', 'professores', 'salas', 'professoresVinculados'));
    }
    public function update(Request $request, Turma $turma)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'professores' => ['required', 'array'],
            'professores.*' => ['exists:professores,id'],
            'sala_id' => ['required', 'exists:salas,id'],
        ]);

        $turma->update([
            'nome' => $request->nome,
            'sala_id' => $request->sala_id,
        ]);

        // Atualiza vínculo com professores (turma_professor)
        $turma->professores()->sync($request->professores);

        // ➕ Sempre vincula professores à NOVA sala
        $novaSala = Sala::find($request->sala_id);
        $novaSala->professores()->syncWithoutDetaching($request->professores);

        return redirect()->route('admin.turmas.index')
            ->with('success', 'Turma atualizada com sucesso!');
    }

    public function destroy(Turma $turma)
    {
        // Nota: O onDelete('cascade') na pivot professor_turma garante a limpeza dos vínculos.
        $turma->delete();
        return redirect()->route('admin.turmas.index')->with('success', 'Turma excluída com sucesso!');
    }
}
