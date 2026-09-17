<?php

use App\Enums\PartidaStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('rodada');

            // O grupo decidiu criar as chaves estrangeiras no fim do
            // desenvolvimento, entao aqui ficam apenas as colunas.
            $table->unsignedBigInteger('time_mandante_id');
            $table->unsignedBigInteger('time_visitante_id');

            $table->string('status', 20)->default(PartidaStatus::Agendada->value);
            $table->dateTime('data_jogo')->nullable();
            $table->unsignedSmallInteger('gols_mandante')->nullable();
            $table->unsignedSmallInteger('gols_visitante')->nullable();
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