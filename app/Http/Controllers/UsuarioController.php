<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\FiltroUsuarioRequest;
use App\Models\User;

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
}