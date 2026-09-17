<?php

namespace App\Policies;

use App\Models\Time;
use App\Models\User;

class TimePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Time $time): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Time $time): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Time $time): bool
    {
        return $user->role === 'admin' && ! $this->possuiPartidas($time);
    }

    private function possuiPartidas(Time $time): bool
    {
        $atributos = $time->getAttributes();

        if (
            array_key_exists('partidas_mandante_count', $atributos)
            && array_key_exists('partidas_visitante_count', $atributos)
        ) {
            return $time->partidas_mandante_count > 0
                || $time->partidas_visitante_count > 0;
        }

        return $time->partidasMandante()->exists()
            || $time->partidasVisitante()->exists();
    }
}
