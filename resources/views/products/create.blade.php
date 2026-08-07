@extends('layouts.main')

@section('title', 'Novo Produto')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-3xl font-bold mb-8">

        Novo Produto

    </h1>

    <form
        action="{{ route('products.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @include('products._form')

    </form>

</div>

@endsection
