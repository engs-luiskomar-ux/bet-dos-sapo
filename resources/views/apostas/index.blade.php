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
    </div>
</x-app-layout>
