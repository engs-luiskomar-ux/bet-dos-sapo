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
    /** @return array<string, mixed> **/
     
    public function definition(): array
    {
        return [
            'rodada' => fake()->numberBetween(1, 38),
            'time_casa_id' => fake()->numberBetween(1, 20),
            'time_fora_id' => fake()->numberBetween(21, 40),
            'status' => PartidaStatus::Agendada,
            'data_jogo' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'gols_casa' => null,
            'gols_fora' => null,
        ];
    }

    /* Partida Encerrada com o placar Sorteado */
        public function finalizada(): static 
        {
            return $this->state(fn (array $attributes) => [
                'status' => PartidaStatus::Finalizada,
                'data_jogo' => fake()->dateTimeBetween('-2 months', '-1 day'),
                'gols_casa' => fake()->numberBetween(0, 4),
                'gols_fora' => fake()->numberBetween(0, 4),
            ]);
        }

        public function naRodada(int $numero): static
        {
            return $this->state(fn (array $attributes) => [
                    'rodada' => $numero,
            ]);
        }
}
