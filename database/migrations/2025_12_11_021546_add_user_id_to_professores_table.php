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
        // ...
        Schema::table('professores', function (Blueprint $table) {
            // Adiciona a FK para o usuário (pode ser nulo se for só professor e não staff)
            $table->foreignId('user_id')
                ->nullable()
                ->after('nome')
                ->constrained()
                ->onDelete('set null');
        });
        // ...
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            //
        });
    }
};
