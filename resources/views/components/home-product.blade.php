<div class="bg-white rounded-xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-200">

    <div class="aspect-square bg-gray-100">

        <img
            src="{{ Storage::url($product->image) }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover"
        >

    </div>

    <div class="p-5">

        <h2 class="text-lg font-bold text-gray-800 line-clamp-2">
            {{ $product->name }}
        </h2>

        <p class="mt-4 text-2xl font-bold text-green-600">
            R$ {{ number_format($product->price, 2, ',', '.') }}
        </p>

        <a href="{{ route('products.show', $product) }}"
            class="mt-6 block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition"
        >
            Ver Produto
        </a>

        <!--
        @unless(auth()->user()->isAdmin())

        <a href="{{ route('products.show', $product) }}"
                    class="mt-3 block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition"
                >
                    Comprar
                </a>

        @endunless
        -->

        @if(!Auth::user()->is_admin)
                    <form
                        method="POST"
                        action="{{ route('cart.store') }}"
                    >
                        @csrf

                        <input
                            type="hidden"
                            name="amount"
                            value="1"
                            min="1"
                        >

                        <button type="submit"
                            class="mt-3 block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition"
                        >
                            Comprar
                        </button>

                    </form>
                    @endif
    </div>

</div>
