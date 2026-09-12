<?php

namespace App\Models;

use App\Enums\PartidaStatus;
use Database\Factories\PartidaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('partidas')]
#[Fillable(['rodada', 'time_casa_id', 'time_fora_id', 'status', 'data_jogo', 'gols_casa', 'gols_fora'])]
class Partida extends Model
{
    /** @use HasFactory<PartidaFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => PartidaStatus::class,
            'data_jogo' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos
    |--------------------------------------------------------------------------
    | Estao declarados, mas so podem ser usados quando os models das outras
    | entregas existirem: Time e do Nelson, Aposta e do Luis. Chamar um deles
    | antes disso gera erro de classe nao encontrada.
    */

    /** @return BelongsTo<Time, $this> */
    public function timeCasa(): BelongsTo
    {
        return $this->belongsTo(Time::class, 'time_casa_id');
    }

    /** @return BelongsTo<Time, $this> */
    public function timeFora(): BelongsTo
    {
        return $this->belongsTo(Time::class, 'time_fora_id');
    }

    /** @return HasMany<Aposta, $this> */
    public function apostas(): HasMany
    {
        return $this->hasMany(Aposta::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Apoio para as telas
    |--------------------------------------------------------------------------
    */

    public function estaAgendada(): bool
    {
        return $this->status === PartidaStatus::Agendada;
    }

    public function placar(): string
    {
        return $this->status === PartidaStatus::Finalizada
            ? "{$this->gols_casa} x {$this->gols_fora}"
            : 'x';
    }

    /**
     * Nome do mandante quando o model Time ja existir; ate la, mostra o id.
     * Quando o Nelson entregar, troque as chamadas na view por
     * $partida->timeCasa->nome e apague estes dois metodos.
     */
    public function rotuloMandante(): string
    {
        return $this->relationLoaded('timeCasa') && $this->timeCasa
            ? $this->timeCasa->nome
            : "Time #{$this->time_casa_id}";
    }

    public function rotuloVisitante(): string
    {
        return $this->relationLoaded('timeFora') && $this->timeFora
            ? $this->timeFora->nome
            : "Time #{$this->time_fora_id}";
    }
}