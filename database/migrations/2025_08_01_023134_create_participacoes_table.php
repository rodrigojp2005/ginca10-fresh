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
        Schema::create('participacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gincana_id')->constrained()->onDelete('cascade');
            $table->integer('pontuacao')->default(0);
            $table->enum('status', ['em_andamento', 'concluida', 'abandonada'])->default('em_andamento');
            $table->timestamp('inicio_participacao')->nullable();
            $table->timestamp('fim_participacao')->nullable();
            $table->integer('tempo_total_segundos')->default(0);
            $table->integer('locais_visitados')->default(0);
            $table->timestamps();
            
            // Evitar participações duplicadas
            $table->unique(['user_id', 'gincana_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participacoes');
    }
};
