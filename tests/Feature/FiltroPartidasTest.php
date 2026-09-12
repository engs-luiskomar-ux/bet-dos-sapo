<?php

namespace Tests\Feature;

use App\Enums\PartidaStatus;
use App\Models\Partida;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltroPartidasTest extends TestCase
{
    use RefreshDatabase;

    public function test_filtra_por_rodada_e_status_na_mesma_pesquisa(): void
    {
        $alvo = Partida::factory()->naRodada(5)->create();
        $outraRodada = Partida::factory()->naRodada(6)->create();
        $outroStatus = Partida::factory()->naRodada(5)->finalizada()->create();

        $resposta = $this->get(route('partidas.index', [
            'rodada' => 5,
            'status' => PartidaStatus::Agendada->value,
        ]));

        $resposta->assertOk();
        $resposta->assertViewHas('partidas', function ($partidas) use ($alvo, $outraRodada, $outroStatus) {
            $ids = $partidas->pluck('id');

            return $ids->contains($alvo->id)
                && ! $ids->contains($outraRodada->id)
                && ! $ids->contains($outroStatus->id);
        });
    }

    public function test_filtra_somente_por_status(): void
    {
        Partida::factory()->count(2)->create();
        $finalizada = Partida::factory()->finalizada()->create();

        $resposta = $this->get(route('partidas.index', [
            'status' => PartidaStatus::Finalizada->value,
        ]));

        $resposta->assertOk();
        $resposta->assertViewHas('partidas', function ($partidas) use ($finalizada) {
            return $partidas->count() === 1
                && $partidas->first()->is($finalizada);
        });
    }

    public function test_sem_filtro_lista_todas_as_partidas(): void
    {
        Partida::factory()->count(3)->create();

        $resposta = $this->get(route('partidas.index'));

        $resposta->assertOk();
        $resposta->assertViewHas('partidas', fn ($partidas) => $partidas->count() === 3);
    }

    public function test_filtro_sem_resultado_devolve_lista_vazia(): void
    {
        Partida::factory()->naRodada(1)->create();

        $resposta = $this->get(route('partidas.index', ['rodada' => 30]));

        $resposta->assertOk();
        $resposta->assertViewHas('partidas', fn ($partidas) => $partidas->isEmpty());
        $resposta->assertSee('Nenhuma partida encontrada para os filtros');
    }

    public function test_status_invalido_e_rejeitado(): void
    {
        $resposta = $this->get(route('partidas.index', ['status' => 'inventado']));

        $resposta->assertSessionHasErrors('status');
    }

    public function test_rodada_fora_do_intervalo_e_rejeitada(): void
    {
        $resposta = $this->get(route('partidas.index', ['rodada' => 99]));

        $resposta->assertSessionHasErrors('rodada');
    }

    public function test_filtros_sao_mantidos_na_paginacao(): void
    {
        Partida::factory()->count(15)->naRodada(3)->create();

        $resposta = $this->get(route('partidas.index', [
            'rodada' => 3,
            'status' => PartidaStatus::Agendada->value,
        ]));

        $resposta->assertOk();
        $resposta->assertSee('rodada=3', false);
        $resposta->assertSee('status='.PartidaStatus::Agendada->value, false);
    }

    public function test_detalhes_da_partida_abrem(): void
    {
        $partida = Partida::factory()->finalizada()->create();

        $resposta = $this->get(route('partidas.show', $partida));

        $resposta->assertOk();
        $resposta->assertSee('Rodada '.$partida->rodada);
    }

    /*
    |--------------------------------------------------------------------------
    | Dependem do model Aposta (entrega do Luis)
    |--------------------------------------------------------------------------
    | Quando ele existir: ligue o withCount('apostas') no controller e o
    | loadCount('apostas') no show, apague os markTestSkipped e crie a aposta
    | com a factory do Luis.
    */

    public function test_exibe_aviso_quando_a_partida_tem_palpites(): void
    {
        $this->markTestSkipped('Depende do model Aposta (entrega do Luis).');

        $partida = Partida::factory()->create();
        // Aposta::factory()->create(['partida_id' => $partida->id]);

        $resposta = $this->get(route('partidas.show', $partida));

        $resposta->assertOk();
        $resposta->assertSee('histórico de palpites', false);
    }

    public function test_edicao_direta_continua_bloqueada_mesmo_sem_o_botao(): void
    {
        $this->markTestSkipped('Depende do model Aposta e da rota de edicao.');

        // O botao some da tela, mas a protecao real fica no controller:
        // uma requisicao montada na mao tem que ser barrada do mesmo jeito.
    }
}