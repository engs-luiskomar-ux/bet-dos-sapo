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
        $admin = User::factory()->create();
        User::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@teste.com']);
        User::factory()->create(['name' => 'Joao Souza', 'email' => 'joao@teste.com']);

        $response = $this->actingAs($admin)->get('/usuarios?busca=Maria');

        $response->assertStatus(200);
        $response->assertSee('Maria Silva');
        $response->assertDontSee('Joao Souza');
    }

    public function test_busca_usuario_por_email(): void
    {
        $admin = User::factory()->create();
        User::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@teste.com']);
        User::factory()->create(['name' => 'Joao Souza', 'email' => 'joao@teste.com']);

        $response = $this->actingAs($admin)->get('/usuarios?busca=joao@teste.com');

        $response->assertStatus(200);
        $response->assertSee('Joao Souza');
        $response->assertDontSee('Maria Silva');
    }

    public function test_lista_vazia_quando_nao_encontra(): void
    {
        $admin = User::factory()->create();

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
}