<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Rodada {{ $partida->rodada }}
            </h2>
            <a href="{{ route('partidas.index') }}" class="text-sm text-gray-600 hover:underline">
                Voltar para a lista
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">
            @if ($partida->apostas_count > 0)
                @include('partidas._aviso-apostas', ['total' => $partida->apostas_count])
            @endif

            <div class="rounded-lg bg-white p-6 shadow">
                <div class="flex items-center justify-center gap-6 text-lg font-medium">
                    <span>{{ $partida->rotuloMandante() }}</span>
                    <span class="font-mono text-gray-700">{{ $partida->placar() }}</span>
                    <span>{{ $partida->rotuloVisitante() }}</span>
                </div>

                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="font-medium">{{ $partida->status->rotulo() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Data</dt>
                        <dd class="font-medium">{{ $partida->data_jogo?->format('d/m/Y H:i') ?? 'a definir' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Palpites</dt>
                        <dd class="font-medium">{{ $partida->apostas_count }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>