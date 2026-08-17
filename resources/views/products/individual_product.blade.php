@extends('layouts.main')

@section('title', $product->name)

@section('content')

<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        <div class="bg-white rounded-xl shadow-md overflow-hidden">

            <img
                src="{{ Storage::url($product->image) }}"
                alt="{{ $product->name }}"
                class="w-full aspect-square object-cover"
            >

        </div>

        <div class="flex flex-col">

            <h1 class="text-4xl font-bold text-gray-800">
                {{ $product->name }}
            </h1>

            <p class="text-4xl font-bold text-green-600 mt-6">
                R$ {{ number_format($product->price, 2, ',', '.') }}
            </p>

            <div class="mt-6 space-y-2">

                <p>
                    <span class="font-semibold">Quantidade:</span>
                    {{ $product->amount}}
                </p>

                <p>
                    <span class="font-semibold">Categoria:</span>
                    {{ $product->category->name }}
                </p>

            </div>

            <hr class="my-6">

            <h2 class="text-xl font-semibold">
                Descrição
            </h2>

            <p class="text-gray-600 mt-3 leading-relaxed">
                {{ $product->description }}
            </p>

            <hr class="my-6">

            <h2 class="text-xl font-semibold">
                Anunciante
            </h2>

            <div class="mt-3 space-y-2">

                <p>
                    <span class="font-semibold">Nome:</span>
                    {{ $product->creator->name }}
                </p>

                <p>
                    <span class="font-semibold">Telefone:</span>
                    {{ $product->creator->phone_number }}
                </p>

            </div>

            @if(!Auth::user()->is_admin)
                <button
                    class="mt-10 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold py-4 rounded-xl transition"
                >
                    Comprar
                </button>
            @endif

        </div>

    </div>

</div>

@endsection
