@extends('layouts.main')

@section('title', 'Meus Produtos')

@section('content')


<div class="max-w-7xl mx-auto py-8">

    @include('components.searchbar', [
            'categories' => $categories,
            'route' => 'products.index',
        ])

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

    @if(Auth::user()->is_admin && !empty($productChartLabels))
        <div class="bg-white rounded-xl shadow p-4 mb-8 max-w-3xl mx-auto">
            <h2 class="text-lg font-semibold mb-3 text-center">Produtos cadastrados</h2>
            <div class="h-52">
                <canvas id="productChart"></canvas>
            </div>
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

                        <td class="p-4 text-center">

                            <img
                                src="{{ Storage::url($product->image) }}"
                                class="w-20 h-20 object-cover rounded"
                            >

                        </td>

                        <td class="p-4 text-center">

                            {{ $product->name }}

                        </td>

                        <td class="p-4 text-center">

                            {{ $product->category->name }}

                        </td>

                        <td class="p-4 text-center">

                            R$ {{ number_format($product->price, 2, ',', '.') }}

                        </td>

                        <td class="p-4 text-center">

                            {{ $product->amount }}

                        </td>

                        <td class="p-4">

                            <div class="flex gap-2 justify-center">

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

@if(Auth::user()->is_admin && !empty($productChartLabels))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const productChartCtx = document.getElementById('productChart');

    new Chart(productChartCtx, {
        type: 'bar',
        data: {
            labels: @json($productChartLabels),
            datasets: [{
                label: 'Produtos cadastrados',
                data: @json($productChartData),
                backgroundColor: '#2563eb',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endif

@endsection
