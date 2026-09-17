<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroTimeRequest;
use App\Http\Requests\TimeRequest;
use App\Models\Time;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class TimeController extends Controller
{
    /**
     * Exibe a lista de times.
     */
    public function index(FiltroTimeRequest $request): View
    {
        Gate::authorize('viewAny', Time::class);

        $busca = $request->string('busca')->toString();

        $times = Time::query()
            ->withCount(['partidasMandante', 'partidasVisitante'])
            ->when($request->filled('busca'), function ($query) use ($busca): void {
                $query->where(function ($query) use ($busca): void {
                    $query->whereLike('nome', "%{$busca}%")
                        ->orWhereLike('sigla', "%{$busca}%");
                });
            })
            ->when(
                $request->filled('estado'),
                fn ($query) => $query->where('estado', $request->string('estado')->toString()),
            )
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        $estados = Time::query()
            ->whereNotNull('estado')
            ->distinct()
            ->orderBy('estado')
            ->pluck('estado');

        return view('times.index', compact('times', 'estados'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create(): View
    {
        Gate::authorize('create', Time::class);

        return view('times.create');
    }

    /**
     * Salva um novo time.
     */
    public function store(TimeRequest $request): RedirectResponse
    {
        Gate::authorize('create', Time::class);

        Time::create($request->validated());

        return redirect()
            ->route('times.index')
            ->with('success', 'Time cadastrado com sucesso!');
    }

    /**
     * Exibe um time específico.
     */
    public function show(Time $time): View
    {
        Gate::authorize('view', $time);

        $time->loadCount(['partidasMandante', 'partidasVisitante']);

        return view('times.show', compact('time'));
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Time $time): View
    {
        Gate::authorize('update', $time);

        return view('times.edit', compact('time'));
    }

    /**
     * Atualiza um time.
     */
    public function update(TimeRequest $request, Time $time): RedirectResponse
    {
        Gate::authorize('update', $time);

        $time->update($request->validated());

        return redirect()
            ->route('times.index')
            ->with('success', 'Time atualizado com sucesso!');
    }

    /**
     * Exclui um time.
     */
    public function destroy(Time $time): RedirectResponse
    {
        Gate::authorize('delete', $time);

        $time->delete();

        return redirect()
            ->route('times.index')
            ->with('success', 'Time excluído com sucesso!');
    }
}
