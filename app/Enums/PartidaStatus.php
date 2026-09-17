<?php

namespace App\Enums;

enum PartidaStatus: string
{
    case Agendada = 'agendada';
    case Finalizada = 'finalizada';

    /* Rotulo */
        public function rotulo(): string
        {
            return match ($this){
                self::Agendada => 'Agendada',
                self::Finalizada => 'Finalizada',
            };
        }
    
    /* Rotulo no plural */
    public function rotuloPlural(): string
        {
            return match ($this) {
                self::Agendada => 'Agendadas',
                self::Finalizada => 'Finalizadas',
            };
        }
}