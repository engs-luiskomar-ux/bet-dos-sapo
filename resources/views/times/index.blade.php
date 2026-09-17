<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Times
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $erro)
                                <li>{{ $erro }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('times._filtros')

                @if($times->isEmpty())
                    <p class="text-gray-500">
                        @if (request()->hasAny(['busca', 'estado']))
                            Nenhum time encontrado para os filtros.
                        @else
                            Nenhum time cadastrado.
                        @endif
                    </p>
                @else
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Nome</th>
                                <th class="py-2">Sigla</th>
                                <th class="py-2">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($times as $time)
                                <tr class="border-b">
                                    <td class="py-2">{{ $time->nome }}</td>
                                    <td class="py-2">{{ $time->sigla }}</td>
                                    <td class="py-2">{{ $time->estado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $times->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
