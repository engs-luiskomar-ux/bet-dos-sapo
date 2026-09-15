<form
    method="GET"
    action="{{ route('apostas.historico') }}"
    class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
>
    <div class="flex flex-wrap items-end gap-4">
        <div>
            <label
                for="status"
                class="block text-sm font-medium text-gray-700"
            >
                Status do palpite
            </label>

            <select
                id="status"
                name="status"
                class="mt-1 rounded-md border-gray-300 shadow-sm"
            >
                <option value="">Todos</option>

            <option
                value="pendente"
                @selected(request('status') === 'pendente')
            >
                Pendentes
            </option>

            <option
                value="ganha"
                @selected(request('status') === 'ganha')
            >
                Ganhas
            </option>

            <option
                value="perdida"
                @selected(request('status') === 'perdida')
            >
                Perdidas
            </option>

            <option
                value="cancelada"
                @selected(request('status') === 'cancelada')
            >
                Canceladas
            </option>
            </select>
        </div>
    </div>
</form>