{{--
    Aviso exibido quando a partida ja tem palpites registrados.
    Uso: @include('partidas._aviso-apostas', ['total' => $partida->apostas_count])
--}}
<div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
    <p class="font-semibold">Este jogo já tem histórico de palpites.</p>
    <p class="mt-1">
        {{ $total }} {{ $total === 1 ? 'palpite foi registrado' : 'palpites foram registrados' }}
        nesta partida, então ela não pode mais ser editada nem excluída. A simulação do
        resultado continua disponível enquanto o jogo estiver agendado.
    </p>
</div>