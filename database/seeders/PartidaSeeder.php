<?php

namespace Database\Seeders;

use App\Enums\PartidaStatus;
use App\Models\Partida;
use App\Models\Time;
use Illuminate\Database\Seeder;

/**
 * Popula os times da Serie A e gera o campeonato inteiro: 38 rodadas de
 * turno e returno, com o turno ja finalizado e o returno agendado.
 *
 * Rode sozinho, sem mexer no DatabaseSeeder (que e compartilhado):
 *     php artisan db:seed --class=PartidaSeeder
 */
class PartidaSeeder extends Seeder
{
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
        $confrontos = $this->confrontos($ids);
        $rodadasDoTurno = $this->rodadasPorTurno(count($ids));

        $linhas = [];

        foreach ($confrontos as $confronto) {
            $linhas[] = $this->linha($confronto, $rodadasDoTurno);
        }

        // Insert em lote: 380 partidas uma a uma no banco remoto demora.
        foreach (array_chunk($linhas, 100) as $lote) {
            Partida::insert($lote);
        }
    }

    /** Com um numero impar de times entra um "fantasma", somando uma rodada. */
    private function rodadasPorTurno(int $quantidadeDeTimes): int
    {
        return $quantidadeDeTimes % 2 === 0
            ? $quantidadeDeTimes - 1
            : $quantidadeDeTimes;
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
     * Round-robin do circulo: no turno cada time enfrenta todos os outros uma
     * vez; o returno repete os mesmos confrontos com o mando invertido.
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
        $rodadasPorTurno = $total - 1;
        $jogosPorRodada = intdiv($total, 2);
        $confrontos = [];

        for ($rodada = 1; $rodada <= $rodadasPorTurno; $rodada++) {
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

                $confrontos[] = [
                    'rodada' => $rodada + $rodadasPorTurno,
                    'mandante' => $visitante,
                    'visitante' => $mandante,
                ];
            }

            // Rotaciona o array mantendo o primeiro time fixo.
            $ultimo = array_pop($times);
            array_splice($times, 1, 0, [$ultimo]);
        }

        return $confrontos;
    }

    /**
     * @param  array{rodada: int, mandante: int, visitante: int}  $confronto
     * @return array<string, mixed>
     */
    private function linha(array $confronto, int $rodadasDoTurno): array
    {
        // O turno ja aconteceu; o returno esta agendado.
        $finalizada = $confronto['rodada'] <= $rodadasDoTurno;

        $dataJogo = now()
            ->subWeeks($rodadasDoTurno)
            ->addWeeks($confronto['rodada'] - 1)
            ->setTime(16, 0)
            ->addHours(random_int(0, 5));

        $agora = now();

        return [
            'rodada' => $confronto['rodada'],
            'time_mandante_id' => $confronto['mandante'],
            'time_visitante_id' => $confronto['visitante'],
            'status' => $finalizada
                ? PartidaStatus::Finalizada->value
                : PartidaStatus::Agendada->value,
            'data_jogo' => $dataJogo,
            'gols_mandante' => $finalizada ? random_int(0, 4) : null,
            'gols_visitante' => $finalizada ? random_int(0, 4) : null,
            'created_at' => $agora,
            'updated_at' => $agora,
        ];
    }
}