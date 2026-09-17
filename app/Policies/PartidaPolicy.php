<?php

namespace App\Policies;

use App\Models\Partida;
use App\Models\User;

class PartidaPolicy
{
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'organizador'], true);
    }

    public function update(User $user, Partida $partida): bool
    {
        $temApostas = array_key_exists('apostas_count', $partida->getAttributes())
            ? $partida->apostas_count > 0
            : $partida->apostas()->exists();

        return $this->create($user)
            && $partida->estaAgendada()
            && ! $temApostas;
    }

    public function delete(User $user, Partida $partida): bool
    {
        return $this->update($user, $partida);
    }

    public function simular(User $user, Partida $partida): bool
    {
        return $this->create($user) && $partida->estaAgendada();
    }
}
