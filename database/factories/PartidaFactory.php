<?php

namespace Database\Factories;

use App\Enums\PartidaStatus;
use App\Models\Partida;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Partida>
 */
class PartidaFactory extends Factory
{
    protected $model = Partida::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'rodada' => fake()->numberBetween(1, 38),
            'time_mandante_id' => fake()->numberBetween(1, 20),
            'time_visitante_id' => fake()->numberBetween(21, 40),
            'status' => PartidaStatus::Agendada,
            'data_jogo' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'gols_mandante' => null,
            'gols_visitante' => null,
        ];
    }

    /** Partida ja encerrada, com placar sorteado. */
    public function finalizada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PartidaStatus::Finalizada,
            'data_jogo' => fake()->dateTimeBetween('-2 months', '-1 day'),
            'gols_mandante' => fake()->numberBetween(0, 4),
            'gols_visitante' => fake()->numberBetween(0, 4),
        ]);
    }

    public function naRodada(int $numero): static
    {
        return $this->state(fn (array $attributes) => [
            'rodada' => $numero,
        ]);
    }
}