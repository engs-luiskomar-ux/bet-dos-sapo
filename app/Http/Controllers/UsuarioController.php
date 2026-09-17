<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\FiltroUsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(FiltroUsuarioRequest $request)
    {
        $busca = trim($request->validated('busca') ?? '');
        $role = $request->validated('role');

        $usuarios = User::query()
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($q) use ($busca) {
                    $q->where('name', 'like', "%{$busca}%")
                      ->orWhere('email', 'like', "%{$busca}%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $papeis = UserRole::cases();

        return view('usuarios.index', compact('usuarios', 'papeis'));
    }

    public function alterarPapel(Request $request, User $usuario): RedirectResponse
    {
        abort_unless($request->user()?->role === UserRole::Admin->value, 403);

        $dados = $request->validate([
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $novoPapel = $dados['role'] instanceof UserRole ? $dados['role']->value : $dados['role'];

        // Se o usuário alvo for Admin e o novo papel não for Admin, verifica quantos existem
        $papelAtual = $usuario->role instanceof UserRole ? $usuario->role->value : $usuario->role;

        if ($papelAtual === UserRole::Admin->value && $novoPapel !== UserRole::Admin->value) {
            $totalAdmins = User::where('role', UserRole::Admin->value)->count();

            if ($totalAdmins <= 1) {
                return back()->with('error', 'Não é possível remover o único administrador do sistema.');
            }
        }

        $usuario->update(['role' => $novoPapel]);

        return back()->with('success', 'Papel atualizado com sucesso.');
    }
}