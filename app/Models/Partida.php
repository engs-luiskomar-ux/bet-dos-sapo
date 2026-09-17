<?php

namespace App\Models;

use App\Enums\PartidaStatus;
use Database\Factories\PartidaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('partidas')]
#[Fillable(['rodada', 'time_mandante_id', 'time_visitante_id', 'status', 'data_jogo', 'gols_mandante', 'gols_visitante'])]
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
    */

    /** @return BelongsTo<Time, $this> */
    public function mandante(): BelongsTo
    {
        return $this->belongsTo(Time::class, 'time_mandante_id');
    }

    /** @return BelongsTo<Time, $this> */
    public function visitante(): BelongsTo
    {
        return $this->belongsTo(Time::class, 'time_visitante_id');
    }

    /** @return HasMany<Aposta, $this> */
    public function apostas(): HasMany
    {
        return $this->hasMany(Aposta::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    | O ApostaController usa Partida::query()->agendadas().
    */

    /** @param  Builder<Partida>  $query */
    public function scopeAgendadas(Builder $query): void
    {
        $query->where('status', PartidaStatus::Agendada);
    }

    /** @param  Builder<Partida>  $query */
    public function scopeFinalizadas(Builder $query): void
    {
        $query->where('status', PartidaStatus::Finalizada);
    }

    /*
    |--------------------------------------------------------------------------
    | Apoio
    |--------------------------------------------------------------------------
    */

    public function estaAgendada(): bool
    {
        return $this->status === PartidaStatus::Agendada;
    }

    /** Usado pelo ApostaService para bloquear e liquidar apostas. */
    public function estaFinalizada(): bool
    {
        return $this->status === PartidaStatus::Finalizada;
    }

    public function placar(): string
    {
        return $this->estaFinalizada()
            ? "{$this->gols_mandante} x {$this->gols_visitante}"
            : 'x';
    }

    /**
     * Nome do time quando o model Time ja estiver na main (entrega do Nelson);
     * ate la, mostra o id para a tela nao quebrar.
     */
    public function rotuloMandante(): string
    {
        return $this->relationLoaded('mandante') && $this->mandante
            ? $this->mandante->nome
            : "Time #{$this->time_mandante_id}";
    }

    public function rotuloVisitante(): string
    {
        return $this->relationLoaded('visitante') && $this->visitante
            ? $this->visitante->nome
            : "Time #{$this->time_visitante_id}";
    }
}