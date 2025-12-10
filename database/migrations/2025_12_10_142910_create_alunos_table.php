<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->id();
            // Dados Pessoais/Contrato
            $table->string('nome_completo', 255);
            $table->date('data_nascimento');
            $table->string('nome_responsavel', 255);
            $table->string('rg', 30);
            $table->string('cpf', 14)->unique();
            $table->string('endereco', 255);
            $table->string('telefone', 20);
            $table->date('data_matricula');
            $table->date('termino_contrato');
            $table->string('periodo', 50); // Ex: Manhã, Tarde
            $table->time('horario'); // Horário de permanência
            $table->string('dias_semana', 100); // Ex: Seg, Qua, Sex

            // Dados Financeiros
            $table->decimal('valor_total', 10, 2);
            $table->decimal('valor_parcela', 10, 2);
            $table->unsignedInteger('quantidade_parcelas');
            $table->string('forma_pagamento', 50);

            // Controle de Documento e Vínculos
            $table->boolean('contrato_gerado')->default(false);
            $table->string('merged_doc_id')->nullable(); // ID/Hash do Contrato
            
            // Relacionamentos FK
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->foreignId('cadastrado_por_user_id')->constrained('users'); // Quem fez a matrícula

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};