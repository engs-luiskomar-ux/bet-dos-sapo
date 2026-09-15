<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
  
    protected $guarded = ['id'];
    
     public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

     public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class); //parte do joao
    }
}
