@extends('partidas._layout')

@section('titulo', 'Partidas')

@section('conteudo')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Partidas</h1>
        <span class="text-sm text-gray-500">{{ $partidas->total() }} jogo(s)</span>
    </div>

    {{-- Filtros: rodada e status na mesma pesquisa --}}
    <form method="GET" action="{{ route('partidas.index') }}"
          class="flex flex-wrap items-end gap-3 rounded-lg bg-white p-4 shadow">
        <div>
            <label for="rodada" class="block text-sm font-medium text-gray-700">Rodada</label>
            <input type="number" id="rodada" name="rodada" min="1" max="38"
                   value="{{ request('rodada') }}" placeholder="Todas"
                   class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="mt-1 block w-44 rounded-md border-gray-300 shadow-sm">
                <option value="">Todos</option>
                @foreach (\App\Enums\PartidaStatus::cases() as $caso)
                    <option value="{{ $caso->value }}" @selected(request('status') === $caso->value)>
                        {{ $caso->rotuloPlural() }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
            Filtrar
        </button>

        @if (request()->hasAny(['rodada', 'status']))
            <a href="{{ route('partidas.index') }}" class="text-sm text-gray-600 hover:underline">Limpar</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Rodada</th>
                    <th class="px-4 py-3">Confronto</th>
                    <th class="px-4 py-3">Placar</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($partidas as $partida)
                    <tr>
                        <td class="px-4 py-3 text-gray-500">{{ $partida->rodada }}</td>
                        <td class="px-4 py-3 font-medium">
                            {{-- Quando o model Time existir, troque por:
                                 {{ $partida->timeCasa->nome }} x {{ $partida->timeFora->nome }} --}}
                            {{ $partida->rotuloMandante() }} x {{ $partida->rotuloVisitante() }}
                        </td>
                        <td class="px-4 py-3 font-mono text-gray-700">{{ $partida->placar() }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded px-2 py-0.5 text-xs font-medium',
                                'bg-blue-100 text-blue-800' => $partida->estaAgendada(),
                                'bg-gray-200 text-gray-700' => ! $partida->estaAgendada(),
                            ])>
                                {{ $partida->status->rotulo() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $partida->data_jogo?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('partidas.show', $partida) }}"
                                   class="text-indigo-600 hover:underline">Detalhes</a>

                                {{-- Com palpites registrados, editar e excluir somem da tela.
                                     O ?? 0 faz isso funcionar agora e continuar funcionando
                                     depois que o withCount('apostas') for ligado. --}}
                                @if (($partida->apostas_count ?? 0) === 0)
                                    {{-- espaco reservado para os botoes Editar e Excluir --}}
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            @if (request()->hasAny(['rodada', 'status']))
                                Nenhuma partida encontrada para os filtros.
                            @else
                                Nenhuma partida cadastrada.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $partidas->links() }}
@endsection