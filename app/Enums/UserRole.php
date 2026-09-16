<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Organizador = 'organizador';
    case Torcedor = 'torcedor';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Organizador => 'Organizador',
            self::Torcedor => 'Torcedor',
        };
    }
}