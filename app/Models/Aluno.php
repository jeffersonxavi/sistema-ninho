<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aluno extends Model
{
    use HasFactory;

    protected $casts = [
        'data_nascimento' => 'date',
        'data_matricula' => 'date',
        'termino_contrato' => 'date',
        'contrato_gerado' => 'boolean',
    ];

    protected $fillable = [
        'nome_completo', 'data_nascimento', 'nome_responsavel', 'rg', 'cpf', 'endereco', 
        'telefone', 'data_matricula', 'termino_contrato', 'periodo', 'horario', 
        'dias_da_semana', // SINCRONIZADO COM A MIGRATION
        
        'valor_total', 'valor_parcela', 
        'qtd_parcelas', // SINCRONIZADO COM A MIGRATION
        
        'forma_pagamento', 'contrato_gerado', 'merged_doc_id', 'turma_id', 
        'cadastrado_por_user_id',
    ];

    // Relacionamentos
    public function turma(): BelongsTo { return $this->belongsTo(Turma::class); }
    public function pagamentos(): HasMany { return $this->hasMany(Pagamento::class); }
    public function cadastradoPor(): BelongsTo { return $this->belongsTo(User::class, 'cadastrado_por_user_id'); }
    public function responsavel(){return $this->belongsTo(Responsavel::class);}
}