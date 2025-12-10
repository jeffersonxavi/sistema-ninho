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

            // Vínculo
            $table->foreignId('aluno_id')->constrained('alunos')->onDelete('cascade');
            
            // Detalhes da Parcela
            $table->unsignedSmallInteger('parcela_numero');
            $table->decimal('valor_previsto', 10, 2);
            $table->date('data_vencimento');
            $table->string('status', 50)->default('Pendente'); 

            // Detalhes do Pagamento (se pago)
            $table->date('data_pagamento')->nullable();
            $table->decimal('valor_pago', 10, 2)->nullable();
            $table->string('metodo_pagamento', 50)->nullable();
            $table->text('observacoes')->nullable();

            // Controle de Usuário
            $table->foreignId('registrado_por_user_id')->constrained('users');

            $table->timestamps();
            
            // Chave única para garantir que um aluno não tenha duas parcelas com o mesmo número
            $table->unique(['aluno_id', 'parcela_numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};