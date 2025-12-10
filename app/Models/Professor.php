<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professor extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'cadastrado_por_user_id',
    ];

    /**
     * Relacionamento: Um Professor tem muitas Turmas.
     */
    public function turmas(): HasMany
    {
        return $this->hasMany(Turma::class);
    }

    /**
     * Relacionamento: Professor foi cadastrado por um usuário (Admin).
     */
    public function cadastradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por_user_id');
    }
}