<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('turma_professor', function (Blueprint $table) {
            // Chave estrangeira para a tabela 'turmas'
            $table->foreignId('turma_id')
                ->constrained('turmas') // <-- tabela correta
                ->onDelete('cascade');

            // Chave estrangeira para a tabela 'professores'
            $table->foreignId('professor_id')
                ->constrained('professores') // <-- tabela correta
                ->onDelete('cascade');

            // Define as duas chaves estrangeiras como Chave Primária Composta
            $table->primary(['turma_id', 'professor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turma_professor');
    }
};
