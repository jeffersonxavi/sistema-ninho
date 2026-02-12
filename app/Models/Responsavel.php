<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responsavel extends Model
{
    protected $table = 'responsaveis';

    protected $fillable = [
        'nome',
        'cpf',
        'rg',
        'telefone',
        'endereco'
    ];

    public function alunos()
    {
        return $this->hasMany(Aluno::class);
    }
}
