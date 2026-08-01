@extends('layouts.main')

@section('title', 'Home')

@section('content')

<div class="max-w-7xl mx-auto py-8">

    <h1 class="text-4xl font-bold mb-8">
        Produtos
    </h1>

    @include('components.searchbar', [
        'categories' => $categories
    ])

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">

        @foreach($products as $product)

            @include('components.home-product', [
                'product' => $product
            ])

        @endforeach

    </div>

    <div class="flex justify-center mt-10">

        {{ $products->links() }}

    </div>

</div>

@endsection
