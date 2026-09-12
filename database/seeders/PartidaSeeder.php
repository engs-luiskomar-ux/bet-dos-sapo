<?php

namespace Database\Seeders;

use App\Models\Partida;
use Illuminate\Database\Seeder;

/**
 * Dados de exemplo para conferir os filtros na tela.
 *
 * Rode sozinho, sem mexer no DatabaseSeeder (que e compartilhado):
 *     php artisan db:seed --class=PartidaSeeder
 */
class PartidaSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 4) as $rodada) {
            Partida::factory()
                ->count(5)
                ->naRodada($rodada)
                ->finalizada()
                ->create();
        }

        foreach (range(5, 8) as $rodada) {
            Partida::factory()
                ->count(5)
                ->naRodada($rodada)
                ->create();
        }
    }
}