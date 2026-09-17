<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Times</h2>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">{{ $times->total() }} time(s)</span>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('times.create') }}"
                       class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">
                        Novo time
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-100 p-4 text-green-800">{{ session('success') }}</div>
                @endif

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

                @if ($times->isEmpty())
                    <p class="py-6 text-center text-gray-500">
                        @if (request()->hasAny(['busca', 'estado']))
                            Nenhum time encontrado para os filtros.
                        @else
                            Nenhum time cadastrado.
                        @endif
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Nome</th>
                                    <th class="px-4 py-3">Sigla</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($times as $time)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $time->nome }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $time->sigla }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $time->estado }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('times.show', $time) }}"
                                               class="text-indigo-600 hover:underline">Ver</a>

                                            @if (auth()->user()->role === 'admin')
                                                <a href="{{ route('times.edit', $time) }}"
                                                   class="ml-3 text-green-700 hover:underline">Editar</a>
                                                <form method="POST" action="{{ route('times.destroy', $time) }}"
                                                      class="ml-3 inline"
                                                      onsubmit="return confirm('Deseja excluir este time?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-700 hover:underline">Excluir</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $times->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
