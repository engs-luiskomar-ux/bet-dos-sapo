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
        return $this->create($user)
            && $partida->estaAgendada()
            && ! $partida->apostas()->exists();
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
