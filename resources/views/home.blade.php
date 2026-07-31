@extends('layouts.main')
@section('title', 'Home')

@section('content')

<div class="container-produtos">
    @foreach($products as $product)
        @include('components.home-product', ['product' => $product])
    @endforeach
</div>

<div class="pagination">
    {{ $products->links() }}
</div>

@endsection
