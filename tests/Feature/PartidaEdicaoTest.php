<?php

namespace Tests\Feature;

use App\Models\Aposta;
use App\Models\Partida;
use App\Models\Time;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartidaEdicaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizador_edita_partida_agendada_sem_apostas(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        [$mandante, $visitante, $novoVisitante] = $this->criarTimes();
        $partida = Partida::factory()->create([
            'rodada' => 2,
            'time_mandante_id' => $mandante->id,
            'time_visitante_id' => $visitante->id,
        ]);

        $response = $this->actingAs($organizador)->patch(route('partidas.update', $partida), [
            'rodada' => 3,
            'time_mandante_id' => $mandante->id,
            'time_visitante_id' => $novoVisitante->id,
            'data_jogo' => '2026-10-10 18:30:00',
        ]);

        $response->assertRedirect(route('partidas.show', $partida));
        $this->assertDatabaseHas('partidas', [
            'id' => $partida->id,
            'rodada' => 3,
            'time_visitante_id' => $novoVisitante->id,
            'data_jogo' => '2026-10-10 18:30:00',
        ]);
    }

    public function test_partida_com_aposta_nao_pode_ser_editada(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);
        $torcedor = User::factory()->create(['role' => 'torcedor']);
        [$mandante, $visitante, $novoVisitante] = $this->criarTimes();
        $partida = Partida::factory()->create([
            'rodada' => 4,
            'time_mandante_id' => $mandante->id,
            'time_visitante_id' => $visitante->id,
        ]);
        Aposta::create([
            'user_id' => $torcedor->id,
            'partida_id' => $partida->id,
            'confronto' => 'Time A x Time B',
            'palpite' => 'mandante',
            'valor' => 100,
            'multiplicador' => 2,
        ]);

        $this->actingAs($organizador)->get(route('partidas.edit', $partida))->assertForbidden();
        $this->actingAs($organizador)->patch(route('partidas.update', $partida), [
            'rodada' => 5,
            'time_mandante_id' => $mandante->id,
            'time_visitante_id' => $novoVisitante->id,
        ])->assertForbidden();

        $this->assertDatabaseHas('partidas', ['id' => $partida->id, 'rodada' => 4]);
    }

    public function test_torcedor_nao_pode_editar_partida(): void
    {
        $torcedor = User::factory()->create(['role' => 'torcedor']);
        $partida = Partida::factory()->create();

        $this->actingAs($torcedor)->get(route('partidas.edit', $partida))->assertForbidden();
        $this->actingAs($torcedor)->patch(route('partidas.update', $partida))->assertForbidden();
    }

    /** @return array{Time, Time, Time} */
    private function criarTimes(): array
    {
        return [
            Time::create(['nome' => 'Time A', 'sigla' => 'TMA', 'estado' => 'PR']),
            Time::create(['nome' => 'Time B', 'sigla' => 'TMB', 'estado' => 'SP']),
            Time::create(['nome' => 'Time C', 'sigla' => 'TMC', 'estado' => 'RJ']),
        ];
    }
}
