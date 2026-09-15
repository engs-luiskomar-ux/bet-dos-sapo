<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroUsuarioRequest;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index(FiltroUsuarioRequest $request)
    {
        $busca = trim($request->validated('busca') ?? '');

        $usuarios = User::query()
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($q) use ($busca) {
                    $q->where('name', 'like', "%{$busca}%")
                      ->orWhere('email', 'like', "%{$busca}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }
}