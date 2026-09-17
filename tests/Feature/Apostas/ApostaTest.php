<?php

namespace Tests\Feature\Apostas;

use App\Models\Aposta;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApostaTest extends TestCase
{
    use RefreshDatabase;

    private function torcedor(): User
    {
        $usuario = User::factory()->create();
        return $usuario->fresh();
    }

    private function registrar(User $usuario, Partida $partida, array $dados = [])
    {
        return $this->actingAs($usuario)->post(route('apostas.store'), array_merge([
            'partida_id' => $partida->id,
            'palpite' => 'mandante',
            'valor' => 100,
        ], $dados));
    }

    public function test_registra_aposta_e_desconta_creditos(): void
    {
        $usuario = $this->torcedor();
        $partida = Partida::factory()->create();

        $this->registrar($usuario, $partida)
            ->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('apostas', [
            'user_id' => $usuario->id,
            'partida_id' => $partida->id,
            'palpite' => 'mandante',
            'valor' => 100,
            'multiplicador' => 2,
            'status' => 'pendente',
        ]);
        $this->assertEquals(900, $usuario->fresh()->saldo_creditos);
    }

    public function test_nao_registra_sem_saldo(): void
    {
        $usuario = $this->torcedor();
        User::whereKey($usuario->id)->update(['saldo_creditos' => 50]);

        $this->registrar($usuario, Partida::factory()->create())
            ->assertSessionHasErrors('valor');

        $this->assertDatabaseCount('apostas', 0);
        $this->assertEquals(50, $usuario->fresh()->saldo_creditos);
    }

    public function test_nao_registra_em_partida_finalizada(): void
    {
        $usuario = $this->torcedor();

        $this->registrar($usuario, Partida::factory()->finalizada()->create())
            ->assertSessionHasErrors('aposta');

        $this->assertDatabaseCount('apostas', 0);
        $this->assertEquals(1000, $usuario->fresh()->saldo_creditos);
    }

    public function test_rejeita_valores_e_palpites_invalidos(): void
    {
        $usuario = $this->torcedor();
        $partida = Partida::factory()->create();

        foreach ([9, 1001, 10.5] as $valor) {
            $this->registrar($usuario, $partida, ['valor' => $valor])
                ->assertSessionHasErrors('valor');
        }

        $this->registrar($usuario, $partida, ['palpite' => 'invalido'])
            ->assertSessionHasErrors('palpite');

        $this->assertDatabaseCount('apostas', 0);
        $this->assertEquals(1000, $usuario->fresh()->saldo_creditos);
    }

    public function test_bloqueia_registro_para_outros_perfis(): void
    {
        $partida = Partida::factory()->create();
        foreach (['admin', 'organizador'] as $perfil) {
            $usuario = User::factory()->create();
            $usuario->forceFill(['role' => $perfil])->save();
            $usuario = $usuario->fresh();
            $this->registrar($usuario, $partida)->assertForbidden();
            $this->assertEquals(1000, $usuario->fresh()->saldo_creditos);
        }

        $this->assertDatabaseCount('apostas', 0);
    }

    public function test_cancela_e_devolve_creditos_apenas_uma_vez(): void
    {
        $usuario = $this->torcedor();
        $this->registrar($usuario, Partida::factory()->create())->assertRedirect();
        $aposta = Aposta::sole();

        $this->post(route('apostas.cancelar', $aposta))
            ->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('apostas', [
            'id' => $aposta->id,
            'status' => 'cancelada',
            'retorno' => 100,
        ]);
        $this->assertEquals(1000, $usuario->fresh()->saldo_creditos);

        $this->post(route('apostas.cancelar', $aposta))
            ->assertSessionHasErrors('aposta');
        $this->assertEquals(1000, $usuario->fresh()->saldo_creditos);
    }

    public function test_nao_cancela_aposta_de_outro_usuario(): void
    {
        $dono = $this->torcedor();
        $this->registrar($dono, Partida::factory()->create())->assertRedirect();
        $aposta = Aposta::sole();

        $this->actingAs($this->torcedor())
            ->post(route('apostas.cancelar', $aposta))->assertForbidden();

        $this->assertSame('pendente', $aposta->fresh()->status);
        $this->assertEquals(900, $dono->fresh()->saldo_creditos);
    }

    public function test_liquida_resultados_e_nao_paga_duas_vezes(): void
    {
        foreach ([[2, 0, 'mandante'], [1, 1, 'empate'], [0, 2, 'visitante']] as [$mandante, $visitante, $resultado]) {
            $partida = Partida::factory()->create();
            $usuarios = [];
            foreach (['mandante', 'empate', 'visitante'] as $palpite) {
                $usuarios[$palpite] = $this->torcedor();
                $this->registrar($usuarios[$palpite], $partida, ['palpite' => $palpite])
                    ->assertSessionHas('success');
            }

            $partida->update([
                'status' => \App\Enums\PartidaStatus::Finalizada,
                'gols_mandante' => $mandante,
                'gols_visitante' => $visitante,
            ]);

            $service = app(\App\Services\ApostaService::class);
            $service->liquidar($partida);
            $service->liquidar($partida);

            foreach ($usuarios as $palpite => $usuario) {
                $retorno = $palpite === $resultado ? 100 * Aposta::OPCOES[$palpite]['multiplicador'] : 0;
                $this->assertEquals(900 + $retorno, $usuario->fresh()->saldo_creditos);
                $this->assertDatabaseHas('apostas', [
                    'user_id' => $usuario->id,
                    'partida_id' => $partida->id,
                    'status' => $palpite === $resultado ? 'ganha' : 'perdida',
                    'retorno' => $retorno,
                    'placar' => $mandante.' × '.$visitante,
                ]);
            }
        }
    }

    public function test_nao_cancela_apos_partida_finalizada(): void
    {
        $usuario = $this->torcedor();
        $partida = Partida::factory()->create();
        $this->registrar($usuario, $partida)->assertRedirect();
        $partida->update([
            'status' => \App\Enums\PartidaStatus::Finalizada,
            'gols_mandante' => 1,
            'gols_visitante' => 0,
        ]);
        $aposta = Aposta::sole();

        $this->post(route('apostas.cancelar', $aposta))
            ->assertSessionHasErrors('aposta');

        $this->assertSame('pendente', $aposta->fresh()->status);
        $this->assertEquals(900, $usuario->fresh()->saldo_creditos);
    }
}
