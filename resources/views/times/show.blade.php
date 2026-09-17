<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detalhes do time</h2>
            <a href="{{ route('times.index') }}" class="text-sm text-gray-600 hover:underline">
                Voltar para a lista
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow">
                <div class="flex items-center gap-5">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-xl font-bold text-green-800">
                        {{ $time->sigla }}
                    </div>
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-900">{{ $time->nome }}</h3>
                        <p class="mt-1 text-gray-500">{{ $time->estado }}</p>
                    </div>
                </div>

                <dl class="mt-8 grid gap-5 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-gray-500">Código</dt>
                        <dd class="font-medium text-gray-900">#{{ $time->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Sigla</dt>
                        <dd class="font-medium text-gray-900">{{ $time->sigla }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Estado</dt>
                        <dd class="font-medium text-gray-900">{{ $time->estado }}</dd>
                    </div>
                </dl>

                @if (auth()->user()->role === 'admin')
                    <div class="mt-8 flex items-center gap-4">
                        <a href="{{ route('times.edit', $time) }}"
                           class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">
                            Editar time
                        </a>

                        <form method="POST" action="{{ route('times.destroy', $time) }}"
                              onsubmit="return confirm('Deseja excluir este time?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white">
                                Excluir time
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
