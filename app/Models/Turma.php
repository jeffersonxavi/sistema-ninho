<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sala_id',
        'cadastrado_por_user_id',
    ];

    public function professores()
    {
        return $this->belongsToMany(Professor::class, 'turma_professor', 'turma_id', 'professor_id');
    }

    /**
     * Relacionamento: Uma Turma pertence a uma Sala (FK: sala_id).
     */
    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }

    /**
     * Relacionamento: Uma Turma tem muitos Alunos.
     */
    public function alunos(): HasMany
    {
        return $this->hasMany(Aluno::class);
    }

    /**
     * Relacionamento: Turma foi cadastrada por um usuário (Admin).
     */
    public function cadastradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por_user_id');
    }
}