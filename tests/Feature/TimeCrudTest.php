<?php

namespace Tests\Feature;

use App\Enums\PartidaStatus;
use App\Models\Partida;
use App\Models\Time;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cadastra_edita_e_exclui_time_sem_partidas(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('times.create'))->assertOk();

        $this->post(route('times.store'), [
            'nome' => 'Paraná Clube',
            'sigla' => 'PAR',
            'estado' => 'PR',
        ])->assertRedirect(route('times.index'));

        $time = Time::sole();
        $this->assertSame('Paraná Clube', $time->nome);

        $this->get(route('times.edit', $time))->assertOk();
        $this->patch(route('times.update', $time), [
            'nome' => 'Paraná',
            'sigla' => 'PRC',
            'estado' => 'PR',
        ])->assertRedirect(route('times.index'));

        $this->assertDatabaseHas('times', [
            'id' => $time->id,
            'nome' => 'Paraná',
            'sigla' => 'PRC',
        ]);

        $this->delete(route('times.destroy', $time))->assertRedirect(route('times.index'));
        $this->assertDatabaseMissing('times', ['id' => $time->id]);
    }

    public function test_cadastro_rejeita_dados_invalidos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('times.store'), [
            'nome' => '',
            'sigla' => '',
            'estado' => 'PARANA',
        ]);

        $response->assertSessionHasErrors(['nome', 'sigla', 'estado']);
        $this->assertDatabaseCount('times', 0);
    }

    public function test_torcedor_e_organizador_nao_podem_alterar_times(): void
    {
        $time = $this->criarTime('Londrina', 'LEC', 'PR');

        foreach (['torcedor', 'organizador'] as $role) {
            $usuario = User::factory()->create(['role' => $role]);

            $this->actingAs($usuario)->get(route('times.create'))->assertForbidden();
            $this->post(route('times.store'), [
                'nome' => 'Novo Time',
                'sigla' => 'NOV',
                'estado' => 'PR',
            ])->assertForbidden();
            $this->get(route('times.edit', $time))->assertForbidden();
            $this->patch(route('times.update', $time), [
                'nome' => 'Alterado',
                'sigla' => 'ALT',
                'estado' => 'SP',
            ])->assertForbidden();
            $this->delete(route('times.destroy', $time))->assertForbidden();
        }

        $this->assertDatabaseHas('times', [
            'id' => $time->id,
            'nome' => 'Londrina',
        ]);
    }

    public function test_admin_nao_exclui_time_vinculado_a_partida(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mandante = $this->criarTime('Maringá', 'MGA', 'PR');
        $visitante = $this->criarTime('Cascavel', 'FCC', 'PR');
        Partida::create([
            'rodada' => 1,
            'time_mandante_id' => $mandante->id,
            'time_visitante_id' => $visitante->id,
            'status' => PartidaStatus::Agendada,
        ]);

        $this->actingAs($admin)
            ->delete(route('times.destroy', $mandante))
            ->assertForbidden();
        $this->delete(route('times.destroy', $visitante))->assertForbidden();

        $this->assertDatabaseHas('times', ['id' => $mandante->id]);
        $this->assertDatabaseHas('times', ['id' => $visitante->id]);
    }

    public function test_torcedor_pode_consultar_detalhes_do_time(): void
    {
        $torcedor = User::factory()->create(['role' => 'torcedor']);
        $time = $this->criarTime('Chapecoense', 'CHA', 'SC');

        $this->actingAs($torcedor)
            ->get(route('times.show', $time))
            ->assertOk()
            ->assertSee('Chapecoense')
            ->assertDontSee('Editar time')
            ->assertDontSee('Excluir time');
    }

    private function criarTime(string $nome, string $sigla, string $estado): Time
    {
        return Time::create(compact('nome', 'sigla', 'estado'));
    }
}
