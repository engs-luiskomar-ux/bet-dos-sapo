<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApostaRequest;
use App\Services\ApostaService;
use Illuminate\Http\RedirectResponse;


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
}
