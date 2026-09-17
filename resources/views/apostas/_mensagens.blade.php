@if (session('success'))
    <div role="status" class="mb-6 rounded-md bg-green-100 p-4 text-green-800">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div role="alert" class="mb-6 rounded-md bg-red-50 p-4 text-red-700">
        <p class="font-semibold">Não foi possível concluir a solicitação.</p>

        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $mensagem)
                <li>{{ $mensagem }}</li>
            @endforeach
        </ul>
    </div>
@endif
