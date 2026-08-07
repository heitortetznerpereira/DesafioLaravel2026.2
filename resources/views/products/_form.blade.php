<div class="space-y-6">

    <div>
        <label class="block font-semibold mb-2">
            Nome
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $product->name ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Preço
        </label>

        <input
            type="number"
            step="0.01"
            name="price"
            value="{{ old('price', $product->price ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Quantidade
        </label>

        <input
            type="number"
            name="amount"
            value="{{ old('amount', $product->amount ?? '') }}"
            class="w-full rounded-lg border px-4 py-2"
            required
        >
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Categoria
        </label>

        <select
            name="category_id"
            class="w-full rounded-lg border px-4 py-2"
            required
        >

            @foreach($categories as $category)

                <option
                    value="{{ $category->id }}"
                    @selected(old('category_id', $product->category_id ?? '') == $category->id)
                >

                    {{ $category->name }}

                </option>

            @endforeach

        </select>
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Descrição
        </label>

        <textarea
            name="description"
            rows="5"
            class="w-full rounded-lg border px-4 py-2"
            required
        >{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="block font-semibold mb-2">
            Foto
        </label>

        <input
            type="file"
            name="image"
            class="w-full"
            accept="image/*"
        >
    </div>

    @isset($product)

        <img
            src="{{ Storage::url($product->image_path) }}"
            class="w-48 rounded-lg shadow"
        >

    @endisset

    <button
        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg"
    >
        Salvar
    </button>

</div>
