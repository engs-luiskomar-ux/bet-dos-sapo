<?php

namespace Database\Seeders;

use App\Enums\PartidaStatus;
use App\Models\Partida;
use App\Models\Time;
use Illuminate\Database\Seeder;

/**
 * Popula times da Serie A e gera as primeiras rodadas do campeonato.
 *
 * Rode sozinho, sem mexer no DatabaseSeeder (que e compartilhado):
 *     php artisan db:seed --class=PartidaSeeder
 */
class PartidaSeeder extends Seeder
{
    /** Quantas rodadas gerar, e quantas delas ja vem com resultado. */
    private const RODADAS = 5;

    private const RODADAS_FINALIZADAS = 3;

    /** @var array<int, array{nome: string, sigla: string, estado: string}> */
    private const TIMES = [
        ['nome' => 'Atletico Mineiro', 'sigla' => 'CAM', 'estado' => 'MG'],
        ['nome' => 'Athletico Paranaense', 'sigla' => 'CAP', 'estado' => 'PR'],
        ['nome' => 'Bahia', 'sigla' => 'BAH', 'estado' => 'BA'],
        ['nome' => 'Botafogo', 'sigla' => 'BOT', 'estado' => 'RJ'],
        ['nome' => 'Corinthians', 'sigla' => 'COR', 'estado' => 'SP'],
        ['nome' => 'Coritiba', 'sigla' => 'CFC', 'estado' => 'PR'],
        ['nome' => 'Cruzeiro', 'sigla' => 'CRU', 'estado' => 'MG'],
        ['nome' => 'Cuiaba', 'sigla' => 'CUI', 'estado' => 'MT'],
        ['nome' => 'Flamengo', 'sigla' => 'FLA', 'estado' => 'RJ'],
        ['nome' => 'Fluminense', 'sigla' => 'FLU', 'estado' => 'RJ'],
        ['nome' => 'Fortaleza', 'sigla' => 'FOR', 'estado' => 'CE'],
        ['nome' => 'Gremio', 'sigla' => 'GRE', 'estado' => 'RS'],
        ['nome' => 'Internacional', 'sigla' => 'INT', 'estado' => 'RS'],
        ['nome' => 'Juventude', 'sigla' => 'JUV', 'estado' => 'RS'],
        ['nome' => 'Palmeiras', 'sigla' => 'PAL', 'estado' => 'SP'],
        ['nome' => 'Red Bull Bragantino', 'sigla' => 'RBB', 'estado' => 'SP'],
        ['nome' => 'Santos', 'sigla' => 'SAN', 'estado' => 'SP'],
        ['nome' => 'Sao Paulo', 'sigla' => 'SAO', 'estado' => 'SP'],
        ['nome' => 'Vasco da Gama', 'sigla' => 'VAS', 'estado' => 'RJ'],
        ['nome' => 'Vitoria', 'sigla' => 'VIT', 'estado' => 'BA'],
    ];

    public function run(): void
    {
        $ids = $this->cadastrarTimes();

        foreach ($this->confrontos($ids) as $confronto) {
            $this->criarPartida($confronto);
        }
    }

    /**
     * Cadastra os times sem duplicar os que ja existirem (a sigla e a chave).
     *
     * @return array<int, int>
     */
    private function cadastrarTimes(): array
    {
        $ids = [];

        foreach (self::TIMES as $time) {
            $ids[] = Time::firstOrCreate(['sigla' => $time['sigla']], $time)->id;
        }

        return $ids;
    }

    /**
     * Monta os confrontos pelo algoritmo round-robin do circulo: em cada
     * rodada, todo time joga uma vez e ninguem se enfrenta duas vezes.
     *
     * @param  array<int, int>  $times
     * @return array<int, array{rodada: int, mandante: int, visitante: int}>
     */
    private function confrontos(array $times): array
    {
        if (count($times) % 2 !== 0) {
            $times[] = null; // time fantasma: quem "enfrenta" ele folga
        }

        $total = count($times);
        $jogosPorRodada = intdiv($total, 2);
        $confrontos = [];

        for ($rodada = 1; $rodada <= self::RODADAS; $rodada++) {
            for ($jogo = 0; $jogo < $jogosPorRodada; $jogo++) {
                $mandante = $times[$jogo];
                $visitante = $times[$total - 1 - $jogo];

                if ($mandante === null || $visitante === null) {
                    continue;
                }

                // Alterna o mando para o mesmo time nao jogar sempre em casa.
                if ($rodada % 2 === 0) {
                    [$mandante, $visitante] = [$visitante, $mandante];
                }

                $confrontos[] = [
                    'rodada' => $rodada,
                    'mandante' => $mandante,
                    'visitante' => $visitante,
                ];
            }

            // Rotaciona o array mantendo o primeiro time fixo.
            $ultimo = array_pop($times);
            array_splice($times, 1, 0, [$ultimo]);
        }

        return $confrontos;
    }

    /** @param  array{rodada: int, mandante: int, visitante: int}  $confronto */
    private function criarPartida(array $confronto): void
    {
        $finalizada = $confronto['rodada'] <= self::RODADAS_FINALIZADAS;

        // Rodada 1 comeca 5 semanas atras; uma rodada por semana.
        $dataJogo = now()
            ->subWeeks(self::RODADAS)
            ->addWeeks($confronto['rodada'] - 1)
            ->setTime(16, 0)
            ->addHours(random_int(0, 5));

        Partida::create([
            'rodada' => $confronto['rodada'],
            'time_mandante_id' => $confronto['mandante'],
            'time_visitante_id' => $confronto['visitante'],
            'status' => $finalizada ? PartidaStatus::Finalizada : PartidaStatus::Agendada,
            'data_jogo' => $finalizada ? $dataJogo : $dataJogo->addWeeks(self::RODADAS + 1),
            'gols_mandante' => $finalizada ? random_int(0, 4) : null,
            'gols_visitante' => $finalizada ? random_int(0, 4) : null,
        ]);
    }
}