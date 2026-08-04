@extends('layouts.main')

@section('title', $product->name)

@section('content')

<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        <div class="bg-white rounded-xl shadow-md overflow-hidden">

            <img
                src="{{ Storage::url($product->image_path) }}"
                alt="{{ $product->name }}"
                class="w-full aspect-square object-cover"
            >

        </div>

        <div class="flex flex-col">

            <span class="text-sm text-gray-500 uppercase">

                {{ $product->category->name }}

            </span>

            <h1 class="text-4xl font-bold text-gray-800 mt-2">

                {{ $product->name }}

            </h1>

            <p class="text-4xl font-bold text-green-600 mt-6">

                R$ {{ number_format($product->price, 2, ',', '.') }}

            </p>

            <hr class="my-6">

            <h2 class="text-xl font-semibold">

                Descrição

            </h2>

            <p class="text-gray-600 mt-3 leading-relaxed">

                {{ $product->description }}

            </p>

            <button
                class="mt-10 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold py-4 rounded-xl transition"
            >
                Comprar
            </button>

        </div>

    </div>

</div>

@endsection
