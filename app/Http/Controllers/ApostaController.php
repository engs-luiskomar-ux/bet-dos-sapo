<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApostaRequest;
use App\Models\Aposta;
use App\Models\Partida;
use App\Services\ApostaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApostaController extends Controller
{
    public function store(
        ApostaRequest $request,
        ApostaService $service
    ): RedirectResponse {
        $dados = $request->validated();

        $service->registrar(
            $request->user(),
            (int) $dados['partida_id'],
            $dados['palpite'],
            (int) $dados['valor']
        );

        return back()->with(
            'success',
            'Palpite registrado com sucesso.'
        );
    }

    public function cancelar(
        Request $request,
        Aposta $aposta,
        ApostaService $service
        ): RedirectResponse {
        $service->cancelar(
            $aposta,
            $request->user()
        );

        return back()->with(
            'success',
            'Palpite cancelado e créditos devolvidos.'
        );
    }

    public function index(): View
    {
        $partidas = Partida::query()
            ->with(['mandante', 'visitante'])
            ->agendadas()
            ->orderBy('rodada')
            ->orderBy('id')
            ->paginate(10);

        return view('apostas.index', compact('partidas'));
    }
}
