<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('saldo_creditos')->default(1000);
        });

        Schema::create('apostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partida_id')
                ->constrained('partidas')
                ->restrictOnDelete();
            $table->string('confronto');
            $table->string('palpite');
            $table->unsignedInteger('valor');
            $table->unsignedInteger('multiplicador');
            $table->unsignedInteger('retorno')->default(0);
            $table->string('status')->default('pendente');
            $table->string('placar')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apostas');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('saldo_creditos');
        });
    }
};