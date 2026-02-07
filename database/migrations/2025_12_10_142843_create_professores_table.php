<?php

use App\Models\Professor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professores', function (Blueprint $table) {
            $table->id();

            // Nome do professor
            $table->string('nome', 150);

            // Relacionamento com o usuário que é o professor (Staff)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Relacionamento com o admin que cadastrou
            $table->foreignId('cadastrado_por_user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });

        // Preenche o campo nome com o name do user vinculado (para registros já existentes)
        Professor::with('user')->get()->each(function ($professor) {
            if ($professor->user) {
                $professor->nome = $professor->user->name;
                $professor->save();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('professores');
    }
};