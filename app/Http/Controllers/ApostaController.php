<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApostaRequest;
use App\Models\Aposta;
use App\Models\Partida;
use App\Services\ApostaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

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

    public function historico(Request $request): View
    {
        $dados = $request->validate([
        'status' => [
            'nullable',
            'string',
            Rule::in([
                'pendente',
                'ganha',
                'perdida',
                'cancelada',
            ]),
        ],
    ]);
        $consulta = Aposta::query()
           ->where('user_id', $request->user()->id);

        if (! empty($dados['status'])) {
            $consulta->where('status', $dados['status']);
        }

        $apostas = $consulta
            ->latest('id')
            ->paginate(15)
            ->withQueryString();
        }
}
