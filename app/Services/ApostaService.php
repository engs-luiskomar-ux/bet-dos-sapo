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
        });
    }
}

