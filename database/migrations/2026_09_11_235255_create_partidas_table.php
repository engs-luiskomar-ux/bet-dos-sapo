<?php

use App\Enums\PartidaStatus;
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
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('rodada');
            
            // Chaves Estrangeiras
            $table->unsignedBigInteger('time_casa_id');
            $table->unsignedBigInteger('time_fora_id');

            $table->string('status', 20)->default(PartidaStatus::Agendada->value);
            $table->datetime('data_jogo')->nullable();
            $table->unsignedSmallInteger('gols_casa')->nullable();
            $table->unsignedSmallInteger('gols_fora')->nullable();
            $table->timestamps();

            $table->index('rodada');
            $table->index('status');     
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
