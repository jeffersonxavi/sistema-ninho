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
            
            // Dados Pessoais/Responsável
            $table->string('nome_completo', 255);
            $table->date('data_nascimento');
            $table->string('nome_responsavel', 255);
            
            // Campos opcionais (nullable)
            $table->string('rg', 30)->nullable();
            $table->string('cpf', 14)->unique()->nullable(); 
            $table->string('endereco', 255)->nullable();
            
            $table->string('telefone', 20); // Obrigatório
            $table->date('data_matricula'); // Obrigatório
            $table->date('termino_contrato')->nullable(); // Opcional
            
            // Dados de Aula/Horário
            $table->string('periodo', 50); // Ex: Manhã, Tarde
            $table->time('horario')->nullable(); // Opcional
            $table->string('dias_da_semana', 255)->nullable(); // CORRIGIDO: usa 'dias_da_semana'
            
            // Dados Financeiros
            $table->decimal('valor_total', 10, 2);
            $table->decimal('valor_parcela', 10, 2);
            $table->unsignedInteger('qtd_parcelas'); // CORRIGIDO: usa 'qtd_parcelas'
            $table->string('forma_pagamento', 50);

            // Controle de Documento e Vínculos
            $table->boolean('contrato_gerado')->default(false);
            $table->string('merged_doc_id')->nullable(); 
            
            // Relacionamentos FK
            $table->foreignId('turma_id')->constrained('turmas')->onDelete('cascade');
            $table->foreignId('cadastrado_por_user_id')->nullable()->constrained('users'); // Staff que gerou
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};