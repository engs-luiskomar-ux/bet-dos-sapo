<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Central de palpites
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Escolha uma partida e registre seu palpite com créditos virtuais.
        </p>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('apostas._mensagens')

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <p class="text-lg font-semibold text-green-900">
                Saldo:
                {{ number_format(auth()->user()->saldo_creditos, 0, ',', '.') }}
                créditos virtuais
            </p>

            <a
                href="{{ route('apostas.historico') }}"
                class="rounded-md bg-gray-700 px-4 py-2 text-white"
            >
                Meus palpites
            </a>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            @forelse ($partidas as $partida)
                <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-green-700">
                        Rodada {{ $partida->rodada }}
                    </p>

                    <h3 class="mt-2 text-lg font-bold text-gray-900">
                        {{ $partida->mandante->nome }}
                        <span class="text-gray-400">×</span>
                        {{ $partida->visitante->nome }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $partida->mandante->sigla }}
                        ×
                        {{ $partida->visitante->sigla }}
                    </p>

                    <form
                        method="POST"
                        action="{{ route('apostas.store') }}"
                        class="mt-5 space-y-4"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="partida_id"
                            value="{{ $partida->id }}"
                        >

                        <fieldset>
                            <legend class="text-sm font-semibold text-gray-700">
                                Seu palpite
                            </legend>

                            <div class="mt-3 grid gap-2 sm:grid-cols-3">
                                @foreach (App\Models\Aposta::OPCOES as $valor => $opcao)
                                    <label
                                        class="flex cursor-pointer items-center gap-2 rounded-md border border-gray-300 p-3"
                                    >
                                        <input
                                            type="radio"
                                            name="palpite"
                                            value="{{ $valor }}"
                                            required
                                            @checked(
                                                old('partida_id') == $partida->id
                                                && old('palpite') === $valor
                                            )
                                        >

                                        <span class="text-sm text-gray-700">
                                            {{ $opcao['nome'] }}
                                            ({{ $opcao['multiplicador'] }}×)
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div>
                            <label
                                for="valor-{{ $partida->id }}"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Créditos da aposta
                            </label>

                            <input
                                id="valor-{{ $partida->id }}"
                                type="number"
                                name="valor"
                                min="10"
                                max="1000"
                                step="1"
                                value="{{ old('partida_id') == $partida->id ? old('valor', 10) : 10 }}"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >

                            <p class="mt-1 text-xs text-gray-500">
                                Use entre 10 e 1.000 créditos inteiros.
                            </p>

                            @if (old('partida_id') == $partida->id)
                                <x-input-error
                                    :messages="$errors->get('valor')"
                                    class="mt-2"
                                />
                            @endif
                        </div>
                        <x-primary-button type="submit">
                            Registrar palpite
                        </x-primary-button>
                    </form>
                </article>
            @empty
                <div class="rounded-xl bg-white p-10 text-center md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Nenhuma partida disponível para palpites.
                    </h3>

                    <p class="mt-2 text-gray-500">
                        Aguarde o cadastro de novas partidas.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $partidas->links() }}
        </div>
    </div>
</x-app-layout>
