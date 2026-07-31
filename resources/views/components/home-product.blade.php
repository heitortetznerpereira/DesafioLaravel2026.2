<div class="bg-blue-950 p-4 justify-center items-center flex flex-col border-t-slate-950 border-t-2">
    <div class="home-product-image">
        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}">
    </div>

    <div class="text-white">
        <h2>{{ $product->name }}</h2>
        <p>Price: ${{ $product->price }}</p>
    </div>
</div>
