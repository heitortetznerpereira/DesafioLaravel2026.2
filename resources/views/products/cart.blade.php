@extends('layouts.main')

@section('title', 'Carrinho de Compras')

@section('content')


<div class="max-w-7xl mx-auto py-8">

    @include('components.searchbar', [
            'categories' => $categories,
            'route' => 'cart.index',
        ])

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">
            Meu Carrinho
        </h1>

        @if($cartProducts->count())
            <form action="{{ route('cart.close') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold"
                >
                    Fechar Carrinho
                </button>
            </form>
        @endif

    </div>

    @if(session('success'))

        <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded-lg mb-6">

            {{ session('success') }}

        </div>

    @endif

    @if(session('error'))

        <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg mb-6">

            {{ session('error') }}

        </div>

    @endif

    <div class="overflow-x-auto">

        <table class="w-full bg-white rounded-lg shadow">

            <thead class="bg-gray-100">

                <tr>

                    <th class="p-4">Imagem</th>

                    <th class="p-4">Nome</th>

                    <th class="p-4">Preço</th>

                    <th class="p-4">Quantidade</th>

                    <th class="p-4">Ações</th>

                </tr>

            </thead>

            <tbody>

                @forelse($cartProducts as $cartProduct)

                    <tr class="border-t">

                        <td class="p-4 text-center">

                            <img
                                src="{{ Storage::url($cartProduct->product->image) }}"
                                class="w-20 h-20 object-cover rounded"
                            >

                        </td>

                        <td class="p-4 text-center">

                            {{ $cartProduct->product->name }}

                        </td>

                        <td class="p-4 text-center">

                            R$ {{ number_format($cartProduct->product->price, 2, ',', '.') }}

                        </td>

                        <td class="p-4 text-center">

                            {{ $cartProduct->amount }}

                        </td>

                        <td class="p-4">

                            <div class="flex gap-2 justify-center">

                                <a
                                    href="{{ route('products.show', $cartProduct->product) }}"
                                    class="bg-green-600 text-white px-4 py-2 rounded"
                                >
                                    Ver
                                </a>

                                <form
                                    action="{{ route('cartProducts.destroy', ['cartProduct' => $cartProduct->id]) }}"
                                    method="POST"
                                    onsubmit="return confirm('Deseja excluir este produto?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="bg-red-600 text-white px-4 py-2 rounded"
                                    >
                                        Excluir
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center p-8 text-gray-500"
                        >

                            Nenhum produto cadastrado.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-8">

        {{ $cartProducts->links() }}

    </div>

</div>

@endsection
