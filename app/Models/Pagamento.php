<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';

    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
        'valor_pago' => 'decimal:2',
    ];

    protected $fillable = [
        'aluno_id',
        'parcela_numero',
        'valor_previsto',
        'data_vencimento',
        'status', // Ex: Pendente, Pago, Atrasado, Cancelado
        'data_pagamento',
        'valor_pago',
        'metodo_pagamento', // Ex: Pix, Boleto, Cartão
        'observacoes',
        'registrado_por_user_id',
    ];

    // Relacionamentos
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por_user_id');
    }
}