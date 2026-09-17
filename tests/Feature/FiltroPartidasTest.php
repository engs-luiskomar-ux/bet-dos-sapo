<?php

namespace Tests\Feature;

use App\Enums\PartidaStatus;
use App\Models\Aposta;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltroPartidasTest extends TestCase
{
    use RefreshDatabase;

    /** As rotas de partidas ficam atras do middleware auth. */
    private function usuario(): User
    {
        return User::factory()->create();
    }

    public function test_visitante_sem_login_e_redirecionado(): void
    {
        $this->get(route('partidas.index'))->assertRedirect(route('login'));
    }

    public function test_filtra_por_rodada_e_status_na_mesma_pesquisa(): void
    {
        $alvo = Partida::factory()->naRodada(5)->create();
        $outraRodada = Partida::factory()->naRodada(6)->create();
        $outroStatus = Partida::factory()->naRodada(5)->finalizada()->create();

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.index', [
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

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.index', [
            'status' => PartidaStatus::Finalizada->value,
        ]));

        $resposta->assertOk();
        $resposta->assertViewHas('partidas', function ($partidas) use ($finalizada) {
            return $partidas->count() === 1 && $partidas->first()->is($finalizada);
        });
    }

    public function test_sem_filtro_lista_todas_as_partidas(): void
    {
        Partida::factory()->count(3)->create();

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.index'));

        $resposta->assertOk();
        $resposta->assertViewHas('partidas', fn ($partidas) => $partidas->count() === 3);
    }

    public function test_filtro_sem_resultado_devolve_lista_vazia(): void
    {
        Partida::factory()->naRodada(1)->create();

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.index', ['rodada' => 30]));

        $resposta->assertOk();
        $resposta->assertViewHas('partidas', fn ($partidas) => $partidas->isEmpty());
        $resposta->assertSee('Nenhuma partida encontrada para os filtros');
    }

    public function test_status_invalido_e_rejeitado(): void
    {
        $resposta = $this->actingAs($this->usuario())
            ->get(route('partidas.index', ['status' => 'inventado']));

        $resposta->assertSessionHasErrors('status');
    }

    public function test_rodada_fora_do_intervalo_e_rejeitada(): void
    {
        $resposta = $this->actingAs($this->usuario())
            ->get(route('partidas.index', ['rodada' => 99]));

        $resposta->assertSessionHasErrors('rodada');
    }

    public function test_filtros_sao_mantidos_na_paginacao(): void
    {
        Partida::factory()->count(15)->naRodada(3)->create();

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.index', [
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

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.show', $partida));

        $resposta->assertOk();
        $resposta->assertSee('Rodada '.$partida->rodada);
    }

    /*
    |--------------------------------------------------------------------------
    | Aviso de historico de palpites
    |--------------------------------------------------------------------------
    */

    public function test_exibe_aviso_quando_a_partida_tem_palpites(): void
    {
        $partida = Partida::factory()->create();
        $this->criarAposta($partida);

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.show', $partida));

        $resposta->assertOk();
        $resposta->assertSee('histórico de palpites', false);
    }

    public function test_palpites_ocultam_edicao_e_exclusao_mas_mantem_simulacao(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $partida = Partida::factory()->create();
        $this->criarAposta($partida);

        $resposta = $this->actingAs($organizador)->get(route('partidas.show', $partida));

        $resposta->assertOk();
        $resposta->assertDontSee('Editar partida');
        $resposta->assertDontSee('Excluir partida');
        $resposta->assertSee('Simular resultado');
    }

    public function test_partida_sem_palpites_nao_mostra_aviso(): void
    {
        $partida = Partida::factory()->create();

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.show', $partida));

        $resposta->assertOk();
        $resposta->assertDontSee('histórico de palpites', false);
    }

    public function test_listagem_conta_os_palpites_de_cada_partida(): void
    {
        $partida = Partida::factory()->create();
        $this->criarAposta($partida);
        $this->criarAposta($partida);

        $resposta = $this->actingAs($this->usuario())->get(route('partidas.index'));

        $resposta->assertOk();
        $resposta->assertViewHas('partidas', fn ($partidas) => $partidas->first()->apostas_count === 2);
    }

    /** Cria uma aposta usando os campos do modulo do Luis. */
    private function criarAposta(Partida $partida): Aposta
    {
        return Aposta::create([
            'user_id' => User::factory()->create()->id,
            'partida_id' => $partida->id,
            'confronto' => 'Mandante × Visitante',
            'palpite' => 'mandante',
            'valor' => 50,
            'multiplicador' => 2,
        ]);
    }
}
