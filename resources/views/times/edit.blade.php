<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar time</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('times.update', $time) }}"
                  class="space-y-6 rounded-lg bg-white p-6 shadow">
                @csrf
                @method('PATCH')

                @include('times._form', ['time' => $time])

                <div class="flex items-center gap-4">
                    <x-primary-button>Salvar alterações</x-primary-button>
                    <a href="{{ route('times.show', $time) }}"
                       class="text-sm text-gray-600 hover:underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
