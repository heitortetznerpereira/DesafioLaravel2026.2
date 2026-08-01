<div class="bg-white rounded-xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-gray-200">

    <div class="h-64 bg-gray-100 flex items-center justify-center">

        <img
            src="{{ Storage::url($product->image_path) }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover"
        >

    </div>

    <div class="p-5">

        <h2 class="text-xl font-bold text-gray-800">
            {{ $product->name }}
        </h2>

        <p class="text-gray-500 mt-2 text-sm h-10 overflow-hidden">
            {{ $product->description }}
        </p>

        <div class="flex items-center justify-between mt-6">

            <span class="text-2xl font-bold text-green-600">
                R$ {{ number_format($product->price, 2, ',', '.') }}
            </span>

            <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg transition duration-300">
                Comprar
            </button>

        </div>

    </div>

</div>
