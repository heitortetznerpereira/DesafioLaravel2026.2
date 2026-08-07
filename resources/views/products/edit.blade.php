@extends('layouts.main')

@section('title', 'Editar Produto')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-3xl font-bold mb-8">
        Editar Produto
    </h1>

    <form
        action="{{ route('products.update', $product) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @method('PUT')

        @include('products._form')

    </form>

</div>

@endsection
