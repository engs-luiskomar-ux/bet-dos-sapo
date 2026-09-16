<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiltroUsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_busca_usuario_por_nome(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@teste.com']);
        User::factory()->create(['name' => 'Joao Souza', 'email' => 'joao@teste.com']);

        $response = $this->actingAs($admin)->get('/usuarios?busca=Maria');

        $response->assertStatus(200);
        $response->assertSee('Maria Silva');
        $response->assertDontSee('Joao Souza');
    }

    public function test_busca_usuario_por_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@teste.com']);
        User::factory()->create(['name' => 'Joao Souza', 'email' => 'joao@teste.com']);

        $response = $this->actingAs($admin)->get('/usuarios?busca=joao@teste.com');

        $response->assertStatus(200);
        $response->assertSee('Joao Souza');
        $response->assertDontSee('Maria Silva');
    }

    public function test_lista_vazia_quando_nao_encontra(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/usuarios?busca=NaoExiste');

        $response->assertStatus(200);
        $response->assertSee('Nenhum usuário encontrado');
    }

    public function test_usuario_nao_autenticado_e_redirecionado(): void
    {
        $response = $this->get('/usuarios');

        $response->assertRedirect('/login');
    }

    public function test_filtra_usuario_por_papel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'Torcedor Um', 'role' => 'torcedor']);
        User::factory()->create(['name' => 'Organizador Um', 'role' => 'organizador']);

        $response = $this->actingAs($admin)->get('/usuarios?role=torcedor');

        $response->assertStatus(200);
        $response->assertSee('Torcedor Um');
        $response->assertDontSee('Organizador Um');
    }

    public function test_torcedor_nao_acessa_lista_de_usuarios(): void
    {
        $torcedor = User::factory()->create(['role' => 'torcedor']);

        $response = $this->actingAs($torcedor)->get('/usuarios');

        $response->assertForbidden();
    }

    public function test_organizador_nao_acessa_lista_de_usuarios(): void
    {
        $organizador = User::factory()->create(['role' => 'organizador']);

        $response = $this->actingAs($organizador)->get('/usuarios');

        $response->assertForbidden();
    }

    public function test_nao_permite_remover_ultimo_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->patch(
            route('usuarios.alterar-papel', $admin),
            ['role' => 'torcedor']
        );

        $response->assertRedirect();
        $this->assertEquals('admin', $admin->fresh()->role);
    }

    public function test_permite_remover_admin_quando_ha_outro(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $outroAdmin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->patch(
            route('usuarios.alterar-papel', $admin),
            ['role' => 'torcedor']
        );

        $response->assertRedirect();
        $this->assertEquals('torcedor', $admin->fresh()->role);
    }
}