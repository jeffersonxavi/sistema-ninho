<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cadastrado_por_user_id',
    ];

    /**
     * Relacionamento: Uma Sala pode ser usada por muitas Turmas.
     */
      public function professores()
    {
        return $this->belongsToMany(Professor::class, 'professor_sala');
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class);
    }

    public function cadastradoPor()
    {
        return $this->belongsTo(User::class, 'cadastrado_por_user_id');
    }
}