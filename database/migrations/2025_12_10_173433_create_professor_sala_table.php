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
        Schema::create('professor_sala', function (Blueprint $table) {
            // Chave estrangeira para a tabela 'professores'
            $table->foreignId('professor_id')
                ->constrained('professores') // <-- tabela correta
                ->onDelete('cascade');

            // Chave estrangeira para a tabela 'salas'
            $table->foreignId('sala_id')
                ->constrained('salas') // <-- tabela correta
                ->onDelete('cascade');

            // Define as duas chaves estrangeiras como Chave Primária Composta
            $table->primary(['professor_id', 'sala_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_sala');
    }
};
