<?php

namespace Tests\Feature;

use App\Models\Partida;
use App\Models\Time;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartidaCadastroTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizador_cadastra_partida_agendada(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $mandante = Time::create(['nome' => 'Time A', 'sigla' => 'TMA', 'estado' => 'PR']);
        $visitante = Time::create(['nome' => 'Time B', 'sigla' => 'TMB', 'estado' => 'SP']);

        $response = $this->actingAs($organizador)->post(route('partidas.store'), [
            'rodada' => 5,
            'time_mandante_id' => $mandante->id,
            'time_visitante_id' => $visitante->id,
            'data_jogo' => '2026-10-01 20:00:00',
        ]);

        $partida = Partida::sole();

        $response->assertRedirect(route('partidas.show', $partida));
        $this->assertDatabaseHas('partidas', [
            'id' => $partida->id,
            'rodada' => 5,
            'status' => 'agendada',
        ]);
    }

    public function test_torcedor_nao_abre_formulario_nem_cadastra_partida(): void
    {
        $torcedor = User::factory()->create(['role' => 'torcedor']);

        $this->actingAs($torcedor)->get(route('partidas.create'))->assertForbidden();
        $this->actingAs($torcedor)->post(route('partidas.store'))->assertForbidden();
    }

    public function test_cadastro_exige_times_diferentes_e_rodada_valida(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $time = Time::create(['nome' => 'Time A', 'sigla' => 'TMA', 'estado' => 'PR']);

        $response = $this->actingAs($organizador)->post(route('partidas.store'), [
            'rodada' => 39,
            'time_mandante_id' => $time->id,
            'time_visitante_id' => $time->id,
        ]);

        $response->assertSessionHasErrors(['rodada', 'time_visitante_id']);
        $this->assertDatabaseCount('partidas', 0);
    }
}
