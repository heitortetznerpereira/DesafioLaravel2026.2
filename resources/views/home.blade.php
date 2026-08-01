@extends('layouts.main')

@section('title', 'Home')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @include('components.searchbar', [
        'categories' => $categories
    ])

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8">

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
