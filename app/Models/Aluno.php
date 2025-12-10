<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aluno extends Model
{
    use HasFactory;

    // Colunas que são datas e precisam ser convertidas (opcional, mas boa prática)
    protected $casts = [
        'data_nascimento' => 'date',
        'data_matricula' => 'date',
        'termino_contrato' => 'date',
        'contrato_gerado' => 'boolean',
        'horario' => 'datetime', // Para facilitar a manipulação de Time
    ];

    // Todos os campos da matrícula (necessário para o CRUD de Aluno)
    protected $fillable = [
        'nome_completo', 'data_nascimento', 'nome_responsavel', 'rg', 'cpf', 'endereco', 
        'telefone', 'data_matricula', 'termino_contrato', 'periodo', 'horario', 
        'dias_semana', 'valor_total', 'valor_parcela', 'quantidade_parcelas', 
        'forma_pagamento', 'contrato_gerado', 'merged_doc_id', 'turma_id', 
        'cadastrado_por_user_id',
    ];

    /**
     * Relacionamento: Um Aluno pertence a uma Turma (FK: turma_id).
     */
    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    /**
     * Relacionamento: Um Aluno tem vários Pagamentos.
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    /**
     * Relacionamento: Aluno foi cadastrado por um usuário (Staff/Admin).
     */
    public function cadastradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por_user_id');
    }
}