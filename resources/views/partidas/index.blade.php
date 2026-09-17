<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Partidas</h2>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ $partidas->total() }} jogo(s)</span>
                @can('create', App\Models\Partida::class)
                    <a href="{{ route('partidas.create') }}" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">
                        Nova partida
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-100 p-4 text-green-800">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Filtros: rodada e status na mesma pesquisa --}}
            <form method="GET" action="{{ route('partidas.index') }}"
                  class="flex flex-wrap items-end gap-3 rounded-lg bg-white p-4 shadow">
                <div>
                    <x-input-label for="rodada" value="Rodada" />
                    <x-text-input id="rodada" name="rodada" type="number" min="1" max="38"
                                  class="mt-1 block w-32" placeholder="Todas" :value="request('rodada')" />
                </div>

                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status"
                            class="mt-1 block w-44 rounded-md border-gray-300 shadow-sm">
                        <option value="">Todos</option>
                        @foreach (\App\Enums\PartidaStatus::cases() as $caso)
                            <option value="{{ $caso->value }}" @selected(request('status') === $caso->value)>
                                {{ $caso->rotuloPlural() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-primary-button>Filtrar</x-primary-button>

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
                            <th class="px-4 py-3">Palpites</th>
                            <th class="px-4 py-3">Data</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($partidas as $partida)
                            <tr>
                                <td class="px-4 py-3 text-gray-500">{{ $partida->rodada }}</td>
                                <td class="px-4 py-3 font-medium">
                                    {{ $partida->rotuloMandante() }} x {{ $partida->rotuloVisitante() }}
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-700">{{ $partida->placar() }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'rounded px-2 py-0.5 text-xs font-medium',
                                        'bg-blue-100 text-blue-800' => $partida->estaAgendada(),
                                        'bg-gray-200 text-gray-700' => $partida->estaFinalizada(),
                                    ])>
                                        {{ $partida->status->rotulo() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $partida->apostas_count }}</td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $partida->data_jogo?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('partidas.show', $partida) }}"
                                       class="text-indigo-600 hover:underline">Detalhes</a>
                                    @can('update', $partida)
                                        <a href="{{ route('partidas.edit', $partida) }}"
                                           class="ml-3 text-green-700 hover:underline">Editar</a>
                                    @endcan
                                    @can('delete', $partida)
                                        <form method="POST" action="{{ route('partidas.destroy', $partida) }}"
                                              class="ml-3 inline"
                                              onsubmit="return confirm('Deseja excluir esta partida?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-700 hover:underline">Excluir</button>
                                        </form>
                                    @endcan
                                    @can('simular', $partida)
                                        <form method="POST" action="{{ route('partidas.simular', $partida) }}"
                                              class="ml-3 inline"
                                              onsubmit="return confirm('Deseja simular o resultado desta partida?')">
                                            @csrf
                                            <button type="submit" class="text-blue-700 hover:underline">Simular</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
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
        </div>
    </div>
</x-app-layout>
