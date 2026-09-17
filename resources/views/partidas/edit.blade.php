<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar partida</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('partidas.update', $partida) }}"
                  class="space-y-6 rounded-lg bg-white p-6 shadow">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="rodada" value="Rodada" />
                    <x-text-input id="rodada" name="rodada" type="number" min="1" max="38"
                                  class="mt-1 block w-full" :value="old('rodada', $partida->rodada)" required />
                    <x-input-error :messages="$errors->get('rodada')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="time_mandante_id" value="Time mandante" />
                    <select id="time_mandante_id" name="time_mandante_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @foreach ($times as $time)
                            <option value="{{ $time->id }}"
                                    @selected((int) old('time_mandante_id', $partida->time_mandante_id) === $time->id)>
                                {{ $time->nome }} ({{ $time->sigla }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('time_mandante_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="time_visitante_id" value="Time visitante" />
                    <select id="time_visitante_id" name="time_visitante_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @foreach ($times as $time)
                            <option value="{{ $time->id }}"
                                    @selected((int) old('time_visitante_id', $partida->time_visitante_id) === $time->id)>
                                {{ $time->nome }} ({{ $time->sigla }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('time_visitante_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="data_jogo" value="Data e hora" />
                    <x-text-input id="data_jogo" name="data_jogo" type="datetime-local"
                                  class="mt-1 block w-full"
                                  :value="old('data_jogo', $partida->data_jogo?->format('Y-m-d\TH:i'))" />
                    <x-input-error :messages="$errors->get('data_jogo')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Salvar alterações</x-primary-button>
                    <a href="{{ route('partidas.show', $partida) }}"
                       class="text-sm text-gray-600 hover:underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
