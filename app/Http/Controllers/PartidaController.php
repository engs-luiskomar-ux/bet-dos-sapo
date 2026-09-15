<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroPartidaRequest;
use App\Models\Partida;
use Illuminate\Contracts\View\View;

class PartidaController extends Controller
{
    /** Listagem com filtro opcional por rodada e por status. */
    public function index(FiltroPartidaRequest $request): View
    {
        $partidas = Partida::query()
            ->with(['mandante', 'visitante'])
            ->withCount('apostas')
            ->when(
                $request->filled('rodada'),
                fn ($query) => $query->where('rodada', $request->integer('rodada')),
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->input('status')),
            )
            ->orderBy('rodada')
            ->orderBy('data_jogo')
            ->paginate(10)
            ->withQueryString();

        return view('partidas.index', compact('partidas'));
    }

    public function show(Partida $partida): View
    {
        $partida->load(['mandante', 'visitante'])->loadCount('apostas');

        return view('partidas.show', compact('partida'));
    }
}