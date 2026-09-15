<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeRequest;
use App\Models\Time;

class TimeController extends Controller
{
    /**
     * Exibe a lista de times.
     */
    public function index()
    {
        $times = Time::orderBy('nome')->paginate(10);

        return view('times.index', compact('times'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create()
    {
        return view('times.create');
    }

    /**
     * Salva um novo time.
     */
    public function store(TimeRequest $request)
    {
        Time::create($request->validated());

        return redirect()
            ->route('times.index')
            ->with('success', 'Time cadastrado com sucesso!');
    }

    /**
     * Exibe um time específico.
     */
    public function show(Time $time)
    {
        return view('times.show', compact('time'));
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Time $time)
    {
        return view('times.edit', compact('time'));
    }

    /**
     * Atualiza um time.
     */
    public function update(TimeRequest $request, Time $time)
    {
        $time->update($request->validated());

        return redirect()
            ->route('times.index')
            ->with('success', 'Time atualizado com sucesso!');
    }

    /**
     * Exclui um time.
     */
    public function destroy(Time $time)
    {
        $time->delete();

        return redirect()
            ->route('times.index')
            ->with('success', 'Time excluído com sucesso!');
    }
}