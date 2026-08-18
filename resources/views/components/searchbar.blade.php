<!-- TODO : mudar placeholder -->
<form method="GET" action="{{ route($route) }}"
          class="flex flex-col md:flex-row gap-4 mb-8">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Pesquisar..."
            class="flex-1 rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        >

        @isset($categories)

        <select
            name="category"
            class="w-full md:w-64 rounded-lg border border-gray-300 px-4 py-3"
        >

            <option value="">Todas as categorias</option>

            @foreach($categories as $category)

                <option
                    value="{{ $category->id }}"
                    @selected(request('category') == $category->id)
                >
                    {{ $category->name }}
                </option>

            @endforeach

        </select>

        @endisset

        <button
            class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-6 py-3 transition"
        >
            Buscar
        </button>

    </form>
