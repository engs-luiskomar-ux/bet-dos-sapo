<form method="GET" action="{{ route('usuarios.index') }}" class="mb-6 flex gap-2">
    <input
        type="text"
        name="busca"
        value="{{ request('busca') }}"
        placeholder="Buscar por nome ou e-mail"
        class="border-gray-300 rounded-md shadow-sm flex-1"
    >
    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">
        Filtrar
    </button>
    @if(request('busca'))
        <a href="{{ route('usuarios.index') }}" class="px-4 py-2 text-gray-600">
            Limpar
        </a>
    @endif
</form>