<?php

namespace App\Services;

use App\Models\Aposta;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApostaService
{
    public function registrar(
        User $usuario,
        int $partidaId,
        string $palpite,
        int $valor
    ): void {
        abort_unless($usuario->role === 'torcedor', 403);

        DB::transaction(function () use (
            $usuario,
            $partidaId,
            $palpite,
            $valor
        ) {
            $partida = Partida::query()
                ->lockForUpdate()
                ->findOrFail($partidaId);

            $carteira = User::query()
                ->lockForUpdate()
                ->findOrFail($usuario->id);

        if ($partida->estaFinalizada()) {
                throw ValidationException::withMessages([
                    'aposta' => 'Esta partida já foi encerrada.',
                ]);
            }
            if (! isset(Aposta::OPCOES[$palpite])) {
                throw ValidationException::withMessages([
                    'palpite' => 'Escolha um palpite válido.',
                ]);
            }
            if ($valor < 10 || $valor > 1000) {
                throw ValidationException::withMessages([
                    'valor' => 'Use entre 10 e 1.000 creditos.',
                ]);
            }
            if ($carteira->saldo_creditos < $valor) {
                throw ValidationException::withMessages([
                    'valor' => 'Saldo de creditos insuficiente.',
                ]);
            }
        });
    }
}

