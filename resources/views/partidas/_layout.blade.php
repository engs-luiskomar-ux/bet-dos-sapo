{{--
    Layout temporario do modulo de partidas.

    O projeto ainda nao tem o Breeze instalado, entao nao existe
    <x-app-layout>. Quando a autenticacao entrar, troque nas telas deste
    modulo o @extends('partidas._layout') / @section('conteudo') pelo
    layout oficial do grupo e apague este arquivo.
--}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Partidas') — Bet dos Sapo Véio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
            <a href="{{ route('partidas.index') }}" class="font-bold">Bet dos Sapo Véio</a>
            <span class="text-sm text-gray-500">Partidas</span>
        </div>
    </header>

    <main class="mx-auto max-w-5xl space-y-4 px-4 py-8">
        @if ($errors->any())
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('conteudo')
    </main>
</body>
</html>