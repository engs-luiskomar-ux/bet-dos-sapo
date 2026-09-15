<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relation\BelongsTo;

class Aposta extends Model
{
    public const OPCOES = [
        'mandante' =>  [

            'nome' => 'Mandante',
            'multiplicador' => 2,
        ],
        'empate' => [
            'nome' =>  'Empate',
            'multiplicador' => 3,
        ],
        'visitante' => [
            'nome' => 'Visitante',
            'multiplicador' => 3,
        ],
    ];

 
}
