<?php

namespace App\Http\Controllers;

use App\Enums\PartidaStatus;
use App\Http\Requests\FiltroPartidaRequest;
use App\Http\Requests\PartidaRequest;
use App\Models\Partida;
use App\Models\Time;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

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

    public function create(): View
    {
        Gate::authorize('create', Partida::class);

        $times = Time::query()->orderBy('nome')->get();

        return view('partidas.create', compact('times'));
    }

    public function store(PartidaRequest $request): RedirectResponse
    {
        $partida = Partida::create([
            ...$request->validated(),
            'status' => PartidaStatus::Agendada,
        ]);

        return redirect()
            ->route('partidas.show', $partida)
            ->with('success', 'Partida cadastrada com sucesso.');
    }

    public function edit(Partida $partida): View
    {
        Gate::authorize('update', $partida);

        $times = Time::query()->orderBy('nome')->get();

        return view('partidas.edit', compact('partida', 'times'));
    }

    public function update(PartidaRequest $request, Partida $partida): RedirectResponse
    {
        Gate::authorize('update', $partida);

        $partida->update($request->validated());

        return redirect()
            ->route('partidas.show', $partida)
            ->with('success', 'Partida atualizada com sucesso.');
    }

    public function destroy(Partida $partida): RedirectResponse
    {
        Gate::authorize('delete', $partida);

        $partida->delete();

        return redirect()
            ->route('partidas.index')
            ->with('success', 'Partida excluída com sucesso.');
    }
}
