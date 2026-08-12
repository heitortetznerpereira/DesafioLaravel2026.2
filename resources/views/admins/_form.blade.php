@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif



@php
    $readonly = $readonly ?? false;
@endphp

<fieldset @disabled($readonly)>
<div class="space-y-6">

    {{-- Nome --}}
    <div>
        <label
            for="name"
            class="block font-semibold mb-2"
        >
            Nome
        </label>

        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name', $user->name ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>


    {{-- Email --}}
    <div>
        <label
            for="email"
            class="block font-semibold mb-2"
        >
            Email
        </label>

        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $user->email ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>


    {{-- Senha --}}
    <div>
        <label
            for="password"
            class="block font-semibold mb-2"
        >
            Senha
        </label>

        <input
            id="password"
            type="password"
            name="password"
            class="w-full rounded-lg border px-4 py-2"
            {{ isset($user) ? '' : 'required' }}
        >

        @isset($user)
        @unless($readonly)

            <p class="text-sm text-gray-500 mt-1">
                Deixe vazio para manter a senha atual.
            </p>

            @endunless
        @endisset
    </div>


    {{-- Telefone --}}
    <div>
        <label
            for="phone_number"
            class="block font-semibold mb-2"
        >
            Número de telefone
        </label>

        <input
            id="phone_number"
            type="text"
            name="phone_number"
            value="{{ old('phone_number', $user->phone_number ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>


    {{-- CPF --}}
    <div>
        <label
            for="cpf"
            class="block font-semibold mb-2"
        >
            CPF
        </label>

        <input
            id="cpf"
            type="text"
            name="cpf"
            value="{{ old('cpf', $user->cpf ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <input type="hidden" name="is_admin" value="true">

    {{-- Data de nascimento --}}
    <div>
        <label
            for="birth_date"
            class="block font-semibold mb-2"
        >
            Data de nascimento
        </label>

        <input
            id="birth_date"
            type="date"
            name="birth_date"
            value="{{ old('birth_date', $user->birth_date ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>


    @unless($readonly)
    {{-- Foto --}}
    <div>
        <label
            for="image"
            class="block font-semibold mb-2"
        >
            Foto
        </label>

        <input
            id="image"
            type="file"
            name="image"
            class="w-full"
            accept="image/*"
        >
    </div>

    @endunless

    {{-- Foto atual --}}
    @isset($user)

        @if($user->image)

            <div>
                <p class="font-semibold mb-2">
                    Foto atual
                </p>

                <img
                    src="{{ Storage::url($user->image) }}"
                    alt="{{ $user->name }}"
                    class="w-48 h-48 object-cover rounded-lg shadow"
                >
            </div>

        @endif

    @endisset



    @unless($readonly)
    {{-- Botão --}}
    <button
        type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg"
    >
        Salvar
    </button>
    @endunless
</fieldset>

</div>
