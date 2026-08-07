@extends('layouts.main')

@section('title', 'Meus Produtos')

@section('content')

<div class="max-w-7xl mx-auto py-8">

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">
            Meus Produtos
        </h1>

        @unless(Auth::user()->is_admin)
        <a
            href="{{ route('products.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg"
        >
            Novo Produto
        </a>
        @endunless

    </div>

    @if(session('success'))

        <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded-lg mb-6">

            {{ session('success') }}

        </div>

    @endif

    <div class="overflow-x-auto">

        <table class="w-full bg-white rounded-lg shadow">

            <thead class="bg-gray-100">

                <tr>

                    <th class="p-4">Imagem</th>

                    <th class="p-4">Nome</th>

                    <th class="p-4">Categoria</th>

                    <th class="p-4">Preço</th>

                    <th class="p-4">Quantidade</th>

                    <th class="p-4">Ações</th>

                </tr>

            </thead>

            <tbody>

                @forelse($products as $product)

                    <tr class="border-t">

                        <td class="p-4">

                            <img
                                src="{{ Storage::url($product->image_path) }}"
                                class="w-20 h-20 object-cover rounded"
                            >

                        </td>

                        <td class="p-4">

                            {{ $product->name }}

                        </td>

                        <td class="p-4">

                            {{ $product->category->name }}

                        </td>

                        <td class="p-4">

                            R$ {{ number_format($product->price, 2, ',', '.') }}

                        </td>

                        <td class="p-4">

                            {{ $product->amount }}

                        </td>

                        <td class="p-4">

                            <div class="flex gap-2">

                                <a
                                    href="{{ route('products.show', $product) }}"
                                    class="bg-green-600 text-white px-4 py-2 rounded"
                                >
                                    Ver
                                </a>

                                <a
                                    href="{{ route('products.edit', $product) }}"
                                    class="bg-yellow-500 text-white px-4 py-2 rounded"
                                >
                                    Editar
                                </a>

                                <form
                                    action="{{ route('products.destroy', $product) }}"
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

        {{ $products->links() }}

    </div>

</div>

@endsection
