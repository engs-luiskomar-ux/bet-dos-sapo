<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Início</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-2xl bg-green-900 p-6 text-white sm:p-8">
            <p class="text-sm font-semibold text-green-200">BET DOS SAPO</p>
            <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Olá, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-green-100">Escolha abaixo o que você quer fazer.</p>
            @if (auth()->user()->role === 'torcedor')
                <p class="mt-5 text-lg font-semibold">
                    Seu saldo: {{ number_format(auth()->user()->saldo_creditos, 0, ',', '.') }} créditos virtuais
                </p>
            @endif
        </div>

        <h2 class="mb-4 text-lg font-semibold text-gray-900">Acesso rápido</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('partidas.index') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-green-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-green-600">
                <h3 class="text-lg font-bold text-gray-900">Partidas</h3>
                <p class="mt-2 text-sm text-gray-600">Veja os confrontos, as rodadas e os resultados.</p>
                <span class="mt-5 inline-block font-semibold text-green-700">Ver partidas →</span>
            </a>
            @if (auth()->user()->role === 'torcedor')
                <a href="{{ route('apostas.index') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-green-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-green-600">
                    <h3 class="text-lg font-bold text-gray-900">Fazer um palpite</h3>
                    <p class="mt-2 text-sm text-gray-600">Escolha seu resultado usando créditos virtuais.</p>
                    <span class="mt-5 inline-block font-semibold text-green-700">Escolher partida →</span>
                </a>
                <a href="{{ route('apostas.historico') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-green-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-green-600">
                    <h3 class="text-lg font-bold text-gray-900">Meus palpites</h3>
                    <p class="mt-2 text-sm text-gray-600">Acompanhe seus palpites e os créditos recebidos.</p>
                    <span class="mt-5 inline-block font-semibold text-green-700">Abrir histórico →</span>
                </a>
            @endif
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('usuarios.index') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-green-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-green-600">
                    <h3 class="text-lg font-bold text-gray-900">Usuários</h3>
                    <p class="mt-2 text-sm text-gray-600">Pesquise os usuários e gerencie os perfis de acesso.</p>
                    <span class="mt-5 inline-block font-semibold text-green-700">Gerenciar usuários →</span>
                </a>
            @endif
            @if (view()->exists('times.index'))
                <a href="{{ route('times.index') }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-green-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-green-600">
                    <h3 class="text-lg font-bold text-gray-900">Times</h3>
                    <p class="mt-2 text-sm text-gray-600">Conheça os times do campeonato.</p>
                    <span class="mt-5 inline-block font-semibold text-green-700">Ver times →</span>
                </a>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6">
                    <h3 class="text-lg font-bold text-gray-600">Times</h3>
                    <p class="mt-2 text-sm text-gray-500">Disponível em breve.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
