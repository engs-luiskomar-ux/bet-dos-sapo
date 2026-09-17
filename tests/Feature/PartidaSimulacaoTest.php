<?php

namespace Tests\Feature;

use App\Models\Aposta;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartidaSimulacaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizador_simula_partida_e_liquida_as_apostas(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $partida = Partida::factory()->create();
        $apostadores = [];

        foreach (['mandante', 'empate', 'visitante'] as $palpite) {
            $apostadores[$palpite] = User::factory()->create([
                'role' => 'torcedor',
                'saldo_creditos' => 900,
            ]);
            Aposta::create([
                'user_id' => $apostadores[$palpite]->id,
                'partida_id' => $partida->id,
                'confronto' => 'Time A x Time B',
                'palpite' => $palpite,
                'valor' => 100,
                'multiplicador' => Aposta::OPCOES[$palpite]['multiplicador'],
            ]);
        }

        $response = $this->actingAs($organizador)
            ->post(route('partidas.simular', $partida));

        $response->assertRedirect(route('partidas.show', $partida));
        $response->assertSessionHas('success');

        $partida->refresh();
        $this->assertTrue($partida->estaFinalizada());
        $this->assertGreaterThanOrEqual(0, $partida->gols_mandante);
        $this->assertLessThanOrEqual(5, $partida->gols_mandante);
        $this->assertGreaterThanOrEqual(0, $partida->gols_visitante);
        $this->assertLessThanOrEqual(5, $partida->gols_visitante);

        $resultado = $this->resultadoDaPartida($partida);

        foreach ($apostadores as $palpite => $apostador) {
            $ganhou = $palpite === $resultado;
            $retorno = $ganhou ? 100 * Aposta::OPCOES[$palpite]['multiplicador'] : 0;

            $this->assertDatabaseHas('apostas', [
                'user_id' => $apostador->id,
                'partida_id' => $partida->id,
                'status' => $ganhou ? 'ganha' : 'perdida',
                'retorno' => $retorno,
                'placar' => $partida->gols_mandante.' × '.$partida->gols_visitante,
            ]);
            $this->assertSame(900 + $retorno, $apostador->fresh()->saldo_creditos);
        }
    }

    public function test_partida_finalizada_nao_pode_ser_simulada_novamente(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $partida = Partida::factory()->finalizada()->create();
        $placar = [$partida->gols_mandante, $partida->gols_visitante];

        $this->actingAs($organizador)
            ->post(route('partidas.simular', $partida))
            ->assertForbidden();

        $partida->refresh();
        $this->assertSame($placar, [$partida->gols_mandante, $partida->gols_visitante]);
    }

    public function test_torcedor_nao_pode_simular_partida(): void
    {
        $torcedor = User::factory()->create(['role' => 'torcedor']);
        $partida = Partida::factory()->create();

        $this->actingAs($torcedor)
            ->post(route('partidas.simular', $partida))
            ->assertForbidden();

        $this->assertTrue($partida->fresh()->estaAgendada());
    }

    private function resultadoDaPartida(Partida $partida): string
    {
        if ($partida->gols_mandante === $partida->gols_visitante) {
            return 'empate';
        }

        return $partida->gols_mandante > $partida->gols_visitante
            ? 'mandante'
            : 'visitante';
    }
}
