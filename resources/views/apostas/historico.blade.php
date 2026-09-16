<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Meus palpites
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Seu histórico e os créditos de cada resultado.
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
                href="{{ route('apostas.index') }}"
                class="rounded-md bg-green-700 px-4 py-2 text-white"
            >
                Novo palpite
            </a>
        </div>

        @include('apostas._filtros')

        <div class="space-y-4">
            @forelse ($apostas as $aposta)
                <article
                    class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
                >
                    <p class="text-xs text-gray-500">
                        #{{ $aposta->id }}
                        ·
                        {{ $aposta->created_at->format('d/m/Y H:i') }}
                    </p>

                    <h3 class="mt-1 font-bold text-gray-900">
                        {{ $aposta->confronto }}
                    </h3>
                </article>
            @empty
                <div class="rounded-xl bg-white p-10 text-center">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Nenhum palpite encontrado.
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
            {{ App\Models\Aposta::OPCOES[$aposta->palpite]['nome'] }}
            ·
            {{ number_format($aposta->valor, 0, ',', '.') }} créditos
            ·
            {{ $aposta->multiplicador }}×
            </p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
