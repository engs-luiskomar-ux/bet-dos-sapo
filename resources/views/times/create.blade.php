<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Cadastrar time</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('times.store') }}"
                  class="space-y-6 rounded-lg bg-white p-6 shadow">
                @csrf

                @include('times._form', ['time' => null])

                <div class="flex items-center gap-4">
                    <x-primary-button>Cadastrar</x-primary-button>
                    <a href="{{ route('times.index') }}"
                       class="text-sm text-gray-600 hover:underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
