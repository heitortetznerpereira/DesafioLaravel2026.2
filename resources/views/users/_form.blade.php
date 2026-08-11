
<div class="space-y-6">

    <div>
        <label class="block font-semibold mb-2">
            Nome
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $user->name ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Email
        </label>

        <input
            type="email"
            name="email"
            value="{{ old('email', $user->email?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Senha
        </label>

        <input
            type="password"
            name="password"
            value="{{ old('password', $user->password ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <div>
        <label class="block font-semibold mb-2">
            CEP
        </label>

        <input
            type="text"
            name="cep"
            value="{{ old('cep', $user->cep ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Número
        </label>

        <input
            type="text"
            name="number"
            value="{{ old('number', $user->number ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Foto
        </label>

        <input
            type="file"
            name="photo_path"
            class="w-full"
            accept="image/*"
        >
    </div>

    @isset($user)

        <img
            src="{{ Storage::url($user->image_path) }}"
            class="w-48 rounded-lg shadow"
        >

    @endisset

    <button
        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg"
    >
        Salvar
    </button>

</div>
