<?php

namespace Database\Factories;

use App\Enums\PartidaStatus;
use App\Models\Partida;
use App\Models\Time;
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
        [$mandante, $visitante] = $this->doisTimes();

        return [
            'rodada' => fake()->numberBetween(1, 38),
            'time_mandante_id' => $mandante,
            'time_visitante_id' => $visitante,
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

    /**
     * Sorteia dois times diferentes. Se a tabela ainda nao tiver dois
     * cadastrados, cria o que faltar — assim a factory funciona tanto nos
     * testes (banco vazio) quanto em cima dos times ja cadastrados.
     *
     * @return array{int, int}
     */
    private function doisTimes(): array
    {
        $ids = Time::query()->inRandomOrder()->limit(2)->pluck('id')->all();

        while (count($ids) < 2) {
            $ids[] = $this->criarTime()->id;
        }

        return [$ids[0], $ids[1]];
    }

    private function criarTime(): Time
    {
        $sigla = strtoupper(fake()->unique()->lexify('???'));

        return Time::create([
            'nome' => 'Time '.$sigla,
            'sigla' => $sigla,
            'estado' => fake()->randomElement(['PR', 'SP', 'RJ', 'MG', 'RS', 'BA', 'CE']),
        ]);
    }
}