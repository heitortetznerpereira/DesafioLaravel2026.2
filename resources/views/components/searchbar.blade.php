<form
    method="GET"
    action="{{ route('home') }}"
    class="flex gap-4 mb-8"
>

    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Pesquisar produto..."
        class="flex-1 border rounded-lg px-4 py-2"
    >

    <select
        name="category"
        class="border rounded-lg px-4 py-2 pr-10"
        onchange="this.form.submit()"
    >

        <option value="">
            Todas as categorias
        </option>

        @foreach($categories as $category)

            <option
                value="{{ $category->id }}"
                @selected(request('category') == $category->id)
            >

                {{ $category->name }}

            </option>

        @endforeach

    </select>

    <button
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 rounded-lg"
    >
        Buscar
    </button>

</form>
