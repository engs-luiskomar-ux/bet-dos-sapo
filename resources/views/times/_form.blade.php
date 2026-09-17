<div>
    <x-input-label for="nome" value="Nome" />
    <x-text-input id="nome" name="nome" type="text" maxlength="100"
                  class="mt-1 block w-full" :value="old('nome', $time?->nome)" required autofocus />
    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
</div>

<div>
    <x-input-label for="sigla" value="Sigla" />
    <x-text-input id="sigla" name="sigla" type="text" maxlength="10"
                  class="mt-1 block w-full uppercase" :value="old('sigla', $time?->sigla)" required />
    <x-input-error :messages="$errors->get('sigla')" class="mt-2" />
</div>

<div>
    <x-input-label for="estado" value="Estado (UF)" />
    <x-text-input id="estado" name="estado" type="text" minlength="2" maxlength="2"
                  class="mt-1 block w-full uppercase" :value="old('estado', $time?->estado)" required />
    <x-input-error :messages="$errors->get('estado')" class="mt-2" />
</div>
