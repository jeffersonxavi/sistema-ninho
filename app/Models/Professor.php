<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professor extends Model
{
    use HasFactory;
    protected $table = 'professores'; // força o nome correto da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'cadastrado_por_user_id',
        'user_id',
    ];

    public function turmas()
    {
        return $this->belongsToMany(Turma::class, 'turma_professor', 'professor_id', 'turma_id');
    }
    public function professores()
    {
        return $this->belongsToMany(Professor::class, 'turma_professor', 'turma_id', 'professor_id');
    }

    // RELACIONAMENTO N:M COM SALAS
    public function salas()
    {
        return $this->belongsToMany(Sala::class, 'professor_sala', 'professor_id', 'sala_id');
    }

    /**
     * Relacionamento: Professor foi cadastrado por um usuário (Admin).
     */
    public function cadastradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por_user_id');
    }
    // ...
    public function user()
    {
        // Relacionamento Um para Um (Um Professor pode ser um Usuário)
        return $this->belongsTo(User::class);
    }
    // ...
}
