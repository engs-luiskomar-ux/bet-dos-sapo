<form method="GET" action="{{ route('times.index') }}"
      class="mb-6 flex flex-wrap items-end gap-3 rounded-lg bg-gray-50 p-4">
    <div class="min-w-64 flex-1">
        <x-input-label for="busca" value="Nome ou sigla" />
        <x-text-input id="busca" name="busca" type="search"
                      class="mt-1 block w-full" :value="request('busca')"
                      placeholder="Ex.: Flamengo ou FLA" />
    </div>

    <div class="w-48">
        <x-input-label for="estado" value="Estado" />
        <select id="estado" name="estado"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Todos os estados</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                    {{ $estado }}
                </option>
            @endforeach
        </select>
    </div>

    <x-primary-button>Filtrar</x-primary-button>

    @if (request()->hasAny(['busca', 'estado']))
        <a href="{{ route('times.index') }}" class="pb-2 text-sm text-gray-600 hover:underline">
            Limpar
        </a>
    @endif
</form>
