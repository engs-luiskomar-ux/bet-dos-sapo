<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Time extends Model
{
    protected $fillable = [
        'nome',
        'sigla',
        'estado',
    ];

    /** @return HasMany<Partida, $this> */
    public function partidasMandante(): HasMany
    {
        return $this->hasMany(Partida::class, 'time_mandante_id');
    }

    /** @return HasMany<Partida, $this> */
    public function partidasVisitante(): HasMany
    {
        return $this->hasMany(Partida::class, 'time_visitante_id');
    }
}
