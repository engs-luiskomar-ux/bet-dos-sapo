<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Usuários
        </h2>
    </x-slot>

<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md">
                        {{ session('error') }}
                    </div>
                @endif

                @include('usuarios._permissoes')

                @include('usuarios._filtros')

                @if($usuarios->isEmpty())
                    <p class="text-gray-500">
                        @if(request('busca') || request('role'))
                            Nenhum usuário encontrado para os filtros.
                        @else
                            Nenhum usuário cadastrado.
                        @endif
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[500px]">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2">Nome</th>
                                    <th class="py-2">E-mail</th>
                                    <th class="py-2">Papel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $usuario)
                                    <tr class="border-b">
                                        <td class="py-2 whitespace-nowrap">{{ $usuario->name }}</td>
                                        <td class="py-2 whitespace-nowrap">{{ $usuario->email }}</td>
                                        <td class="py-2">
                                            <form method="POST" action="{{ route('usuarios.alterar-papel', $usuario) }}" class="flex gap-2 items-center">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" class="border-gray-300 rounded-md shadow-sm text-sm">
                                                    @foreach($papeis as $papel)
                                                        <option value="{{ $papel->value }}" @selected($usuario->role === $papel->value)>
                                                            {{ $papel->label() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="px-2 py-1 text-xs bg-gray-800 text-white rounded-md">
                                                    Alterar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>