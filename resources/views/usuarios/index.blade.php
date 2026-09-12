<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Usuários
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @include('usuarios._permissoes')

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

                @if($usuarios->isEmpty())
                    <p class="text-gray-500">
                        @if(request('busca'))
                            Nenhum usuário encontrado para os filtros.
                        @else
                            Nenhum usuário cadastrado.
                        @endif
                    </p>
                @else
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">Nome</th>
                                <th class="py-2">E-mail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr class="border-b">
                                    <td class="py-2">{{ $usuario->name }}</td>
                                    <td class="py-2">{{ $usuario->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>