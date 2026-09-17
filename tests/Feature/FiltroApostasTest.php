<?php

namespace Tests\Feature;

use App\Models\Aposta;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltroApostasTest extends TestCase
{
    use RefreshDatabase;

    private function aposta(User $usuario, Partida $partida, string $status): Aposta
    {
        return Aposta::create([
            'user_id' => $usuario->id,
            'partida_id' => $partida->id,
            'confronto' => 'Time A × Time B',
            'palpite' => 'mandante',
            'valor' => 10,
            'multiplicador' => 2,
            'status' => $status,
        ]);
    }

    public function test_visitante_precisa_fazer_login(): void
    {
        $this->get(route('apostas.historico'))->assertRedirect(route('login'));
    }

    public function test_filtra_cada_status_sem_exibir_apostas_de_outro_usuario(): void
    {
        $usuario = User::factory()->create();
        $outro = User::factory()->create();
        $partida = Partida::factory()->create();

        foreach (['pendente', 'ganha', 'perdida', 'cancelada'] as $status) {
            $this->aposta($usuario, $partida, $status);
            $this->aposta($outro, $partida, $status);
        }

        foreach (['pendente', 'ganha', 'perdida', 'cancelada'] as $status) {
            $this->actingAs($usuario)
                ->get(route('apostas.historico', ['status' => $status]))
                ->assertOk()
                ->assertViewHas('apostas', fn ($apostas) =>
                    $apostas->total() === 1
                    && $apostas->first()->user_id === $usuario->id
                    && $apostas->first()->status === $status
                );
        }

        $this->actingAs($usuario)->get(route('apostas.historico'))
            ->assertOk()
            ->assertViewHas('apostas', fn ($apostas) =>
                $apostas->total() === 4
                && $apostas->every(fn ($aposta) => $aposta->user_id === $usuario->id)
            );
    }

    public function test_diferencia_historico_vazio_de_filtro_sem_resultados(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get(route('apostas.historico'))
            ->assertOk()->assertSee('Nenhum palpite cadastrado.');

        $this->aposta($usuario, Partida::factory()->create(), 'pendente');

        $this->get(route('apostas.historico', ['status' => 'ganha']))
            ->assertOk()->assertSee('Nenhum palpite para este filtro.');
    }

    public function test_rejeita_status_invalido(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('apostas.historico'))
            ->get(route('apostas.historico', ['status' => 'invalido']))
            ->assertRedirect(route('apostas.historico'))
            ->assertSessionHasErrors('status');
    }

    public function test_mantem_status_na_paginacao_sem_alterar_saldo_ou_apostas(): void
    {
        $usuario = User::factory()->create();
        $partida = Partida::factory()->create();
        for ($i = 0; $i < 16; $i++) {
            $this->aposta($usuario, $partida, 'cancelada');
        }

        $this->actingAs($usuario)
            ->get(route('apostas.historico', ['status' => 'cancelada']))
            ->assertOk()
            ->assertViewHas('apostas', fn ($apostas) =>
                $apostas->total() === 16
                && str_contains($apostas->nextPageUrl(), 'status=cancelada')
            );

        $this->get(route('apostas.historico', ['status' => 'cancelada', 'page' => 2]))
            ->assertOk()
            ->assertViewHas('apostas', fn ($apostas) => $apostas->count() === 1);

        $this->assertEquals(1000, $usuario->fresh()->saldo_creditos);
        $this->assertSame(16, Aposta::where('status', 'cancelada')->count());
    }
}
