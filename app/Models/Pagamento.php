<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'aluno_id',
        'parcela_numero',
        'valor_previsto',
        'data_vencimento',
        'status',
        'valor_pago',
        'data_pagamento',
        'registrado_por_user_id',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
    ];

    /**
     * Relacionamento: O Pagamento pertence a um Aluno (FK: aluno_id).
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    /**
     * Relacionamento: Quem registrou a baixa do pagamento (Staff/Admin).
     */
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por_user_id');
    }
}