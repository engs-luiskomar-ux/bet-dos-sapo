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
    </div>
</x-app-layout>
