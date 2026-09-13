<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltroTimeRequest;
use App\Http\Requests\TimeRequest;
use App\Models\Time;
use Illuminate\Support\Facades\Gate;

class TimeController extends Controller
{
    public function index(FiltroTimeRequest $request)
    {
        Gate::authorize('viewAny', Time::class);

        $query = Time::query();

        /*
         * Pesquisa por nome OU sigla
         */
        if ($request->filled('busca')) {
            $busca = $request->validated('busca');

            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', '%' . $busca . '%')
                    ->orWhere('sigla', 'like', '%' . $busca . '%');
            });
        }

        /*
         * Filtro por estado
         */
        if ($request->filled('estado')) {
            $query->where('estado', $request->validated('estado'));
        }

        /*
         * Lista de estados existentes no banco
         */
        $estados = Time::query()
            ->whereNotNull('estado')
            ->where('estado', '!=', '')
            ->distinct()
            ->orderBy('estado')
            ->pluck('estado');

        /*
         * Ordenação e paginação
         */
        $times = $query
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return view('times.index', compact('times', 'estados'));
    }

    public function create()
    {
        Gate::authorize('create', Time::class);

        return view('times.create');
    }

    public function store(TimeRequest $request)
    {
        Gate::authorize('create', Time::class);

        Time::create($request->validated());

        return redirect()
            ->route('times.index')
            ->with('success', 'Time cadastrado com sucesso!');
    }

    public function show(Time $time)
    {
        Gate::authorize('view', $time);

        return view('times.show', compact('time'));
    }

    public function edit(Time $time)
    {
        Gate::authorize('update', $time);

        return view('times.edit', compact('time'));
    }

    public function update(TimeRequest $request, Time $time)
    {
        Gate::authorize('update', $time);

        $time->update($request->validated());

        return redirect()
            ->route('times.index')
            ->with('success', 'Time atualizado com sucesso!');
    }

    public function destroy(Time $time)
    {
        Gate::authorize('delete', $time);

        $time->delete();

        return redirect()
            ->route('times.index')
            ->with('success', 'Time excluído com sucesso!');
    }
}