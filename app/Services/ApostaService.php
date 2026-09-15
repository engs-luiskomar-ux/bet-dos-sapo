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

            $carteira->decrement('saldo_creditos', $valor);
            Aposta::create([
                'user_id' => $usuario->id,
                'partida_id' => $partida->id,
                'confronto' => $partida->mandante->nome
                    .' × '
                    .$partida->visitante->nome,
                'palpite' => $palpite,
                'valor' => $valor,
                'multiplicador' =>
                    Aposta::OPCOES[$palpite]['multiplicador'],
            ]);
        });
    }
            public function liquidar(Partida $partida): void
        {
            if (! $partida->estaFinalizada()) {
                return;
    }
            $apostas = Aposta::query()
            ->where('partida_id', $partida->id)
            ->where('status', 'pendente')
            ->lockForUpdate()
            ->get();
            
            private function resultadoDaPartida(Partida $partida): string
        {
            if ($partida->gols_mandante === $partida->gols_visitante) {
                return 'empate';
            }

            return $partida->gols_mandante > $partida->gols_visitante
                ? 'mandante'
                : 'visitante';
        }
}

