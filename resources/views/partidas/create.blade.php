<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Nova partida</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('partidas.store') }}" class="space-y-5 rounded-xl bg-white p-6 shadow">
            @csrf

            <div>
                <x-input-label for="rodada" value="Rodada" />
                <x-text-input id="rodada" name="rodada" type="number" min="1" max="38" class="mt-1 block w-full" :value="old('rodada')" required />
                <x-input-error :messages="$errors->get('rodada')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="time_mandante_id" value="Time mandante" />
                <select id="time_mandante_id" name="time_mandante_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">Selecione</option>
                    @foreach ($times as $time)
                        <option value="{{ $time->id }}" @selected(old('time_mandante_id') == $time->id)>{{ $time->nome }} ({{ $time->sigla }})</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('time_mandante_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="time_visitante_id" value="Time visitante" />
                <select id="time_visitante_id" name="time_visitante_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">Selecione</option>
                    @foreach ($times as $time)
                        <option value="{{ $time->id }}" @selected(old('time_visitante_id') == $time->id)>{{ $time->nome }} ({{ $time->sigla }})</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('time_visitante_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="data_jogo" value="Data e hora" />
                <x-text-input id="data_jogo" name="data_jogo" type="datetime-local" class="mt-1 block w-full" :value="old('data_jogo')" />
                <x-input-error :messages="$errors->get('data_jogo')" class="mt-2" />
            </div>

            <div class="flex gap-3">
                <x-primary-button type="submit">Cadastrar partida</x-primary-button>
                <a href="{{ route('partidas.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>
