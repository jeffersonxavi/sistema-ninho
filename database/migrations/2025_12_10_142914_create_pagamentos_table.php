<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            
            // Vínculo com Aluno
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');

            // Detalhes da Parcela
            $table->unsignedInteger('parcela_numero');
            $table->decimal('valor_previsto', 10, 2);
            $table->date('data_vencimento');
            $table->enum('status', ['Pendente', 'Pago', 'Atrasado', 'Cancelado'])->default('Pendente');

            // Detalhes do Pagamento
            $table->decimal('valor_pago', 10, 2)->nullable();
            $table->date('data_pagamento')->nullable();
            $table->foreignId('registrado_por_user_id')->nullable()->constrained('users'); // Quem deu a baixa

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};