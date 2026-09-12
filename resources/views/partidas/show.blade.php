@extends('partidas._layout')

@section('titulo', 'Detalhes da partida')

@section('conteudo')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Rodada {{ $partida->rodada }}</h1>
        <a href="{{ route('partidas.index') }}" class="text-sm text-gray-600 hover:underline">
            Voltar para a lista
        </a>
    </div>

    @if (($partida->apostas_count ?? 0) > 0)
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
                <dd class="font-medium">{{ $partida->apostas_count ?? 0 }}</dd>
            </div>
        </dl>
    </div>

    @if (($partida->apostas_count ?? 0) === 0)
        {{-- espaco reservado para os botoes Editar e Excluir --}}
    @endif
@endsection