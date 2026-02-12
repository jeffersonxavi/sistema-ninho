<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropColumn([
                'nome_responsavel',
                'telefone',
                'endereco',
                'cpf',
                'rg'
            ]);
        });
    }

    public function down()
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->string('nome_responsavel')->nullable();
            $table->string('telefone')->nullable();
            $table->string('endereco')->nullable();
            $table->string('cpf')->nullable();
            $table->string('rg')->nullable();
        });
    }
};
