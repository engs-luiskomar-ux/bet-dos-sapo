<?php

namespace Tests\Feature;

use App\Models\Aposta;
use App\Models\Partida;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartidaExclusaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizador_exclui_partida_agendada_sem_apostas(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $partida = Partida::factory()->create();

        $response = $this->actingAs($organizador)->delete(route('partidas.destroy', $partida));

        $response->assertRedirect(route('partidas.index'));
        $response->assertSessionHas('success', 'Partida excluída com sucesso.');
        $this->assertDatabaseMissing('partidas', ['id' => $partida->id]);
    }

    public function test_partida_com_aposta_nao_pode_ser_excluida(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $torcedor = User::factory()->create(['role' => 'torcedor']);
        $partida = Partida::factory()->create();
        Aposta::create([
            'user_id' => $torcedor->id,
            'partida_id' => $partida->id,
            'confronto' => 'Time A x Time B',
            'palpite' => 'mandante',
            'valor' => 100,
            'multiplicador' => 2,
        ]);

        $this->actingAs($organizador)
            ->delete(route('partidas.destroy', $partida))
            ->assertForbidden();

        $this->assertDatabaseHas('partidas', ['id' => $partida->id]);
    }

    public function test_partida_finalizada_nao_pode_ser_excluida(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $partida = Partida::factory()->finalizada()->create();

        $this->actingAs($organizador)
            ->delete(route('partidas.destroy', $partida))
            ->assertForbidden();

        $this->assertDatabaseHas('partidas', ['id' => $partida->id]);
    }

    public function test_torcedor_nao_pode_excluir_partida(): void
    {
        $torcedor = User::factory()->create(['role' => 'torcedor']);
        $partida = Partida::factory()->create();

        $this->actingAs($torcedor)
            ->delete(route('partidas.destroy', $partida))
            ->assertForbidden();

        $this->assertDatabaseHas('partidas', ['id' => $partida->id]);
    }
}
