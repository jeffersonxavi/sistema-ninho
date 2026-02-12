<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responsaveis', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->string('cpf', 14)->unique();
            $table->string('rg', 30)->nullable();
            $table->string('telefone', 20);
            $table->string('endereco', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsaveis');
    }
};
