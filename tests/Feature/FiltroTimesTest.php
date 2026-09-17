<?php

namespace Tests\Feature;

use App\Models\Time;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltroTimesTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_sem_login_e_redirecionado(): void
    {
        $this->get(route('times.index'))->assertRedirect(route('login'));
    }

    public function test_pesquisa_por_nome_ou_sigla(): void
    {
        $athletico = $this->criarTime('Athletico Paranaense', 'CAP', 'PR');
        $flamengo = $this->criarTime('Flamengo', 'FLA', 'RJ');
        $this->criarTime('Palmeiras', 'PAL', 'SP');

        $porNome = $this->actingAs($this->torcedor())
            ->get(route('times.index', ['busca' => 'athletico']));
        $porNome->assertOk()->assertViewHas(
            'times',
            fn ($times) => $times->count() === 1 && $times->first()->is($athletico),
        );

        $porSigla = $this->get(route('times.index', ['busca' => 'fla']));
        $porSigla->assertOk()->assertViewHas(
            'times',
            fn ($times) => $times->count() === 1 && $times->first()->is($flamengo),
        );
    }

    public function test_filtra_por_estado_e_combina_com_busca(): void
    {
        $alvo = $this->criarTime('Coritiba', 'CFC', 'PR');
        $this->criarTime('Operário', 'OPE', 'PR');
        $this->criarTime('Corinthians', 'COR', 'SP');

        $response = $this->actingAs($this->torcedor())->get(route('times.index', [
            'busca' => 'cori',
            'estado' => 'pr',
        ]));

        $response->assertOk();
        $response->assertSee($alvo->nome);
        $response->assertDontSee('Operário');
        $response->assertDontSee('Corinthians');
    }

    public function test_filtro_sem_resultado_mostra_mensagem(): void
    {
        $this->criarTime('Grêmio', 'GRE', 'RS');

        $response = $this->actingAs($this->torcedor())
            ->get(route('times.index', ['busca' => 'inexistente']));

        $response->assertOk();
        $response->assertSee('Nenhum time encontrado para os filtros.');
    }

    public function test_rejeita_estado_invalido(): void
    {
        $this->actingAs($this->torcedor())
            ->get(route('times.index', ['estado' => 'PARANA']))
            ->assertSessionHasErrors('estado');
    }

    public function test_mantem_filtros_na_paginacao(): void
    {
        foreach (range(1, 12) as $numero) {
            $this->criarTime("Time Paraná {$numero}", "P{$numero}", 'PR');
        }

        $response = $this->actingAs($this->torcedor())->get(route('times.index', [
            'busca' => 'Paraná',
            'estado' => 'PR',
        ]));

        $response->assertOk();
        $response->assertSee('busca=Paran%C3%A1', false);
        $response->assertSee('estado=PR', false);
    }

    public function test_torcedor_consulta_sem_ver_acoes_administrativas(): void
    {
        $time = $this->criarTime('Bahia', 'BAH', 'BA');

        $response = $this->actingAs($this->torcedor())->get(route('times.index'));

        $response->assertOk()->assertSee($time->nome);
        $response->assertDontSee('Novo time');
        $response->assertDontSee('Editar');
        $response->assertDontSee('Excluir');
    }

    private function torcedor(): User
    {
        return User::factory()->create(['role' => 'torcedor']);
    }

    private function criarTime(string $nome, string $sigla, string $estado): Time
    {
        return Time::create(compact('nome', 'sigla', 'estado'));
    }
}
