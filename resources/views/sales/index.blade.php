    @extends('layouts.main')

    @section('title', 'Histórico de Vendas')

    @section('content')

    <div class="max-w-7xl mx-auto py-8">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">

            <h1 class="text-3xl font-bold">
                Histórico de Vendas
            </h1>

            <form
                method="GET"
                action="{{ route('sales.index') }}"
                class="flex flex-wrap items-end gap-3"
            >

                <div>
                    <label class="block text-sm font-medium">
                        Data inicial
                    </label>

                    <input
                        type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="border rounded-lg px-3 py-2"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium">
                        Data final
                    </label>

                    <input
                        type="date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="border rounded-lg px-3 py-2"
                    >
                </div>

                <button
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700"
                >
                    Filtrar
                </button>

                <a
                    href="{{ route('sales.index') }}"
                    class="bg-gray-500 text-white px-5 py-2 rounded-lg"
                >
                    Limpar
                </a>

            </form>

        </div>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 rounded-lg p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-xl shadow">

        <table class="w-full">

            <thead class="bg-gray-100">

            <tr>

                <th class="p-4">Foto</th>

                <th class="p-4">Produto</th>

                <th class="p-4">Categoria</th>

                <th class="p-4">Comprador</th>

                <th class="p-4">Vendedor</th>

                <th class="p-4">Valor</th>

                <th class="p-4">Data</th>

            </tr>

            </thead>
            <tbody>

            @forelse($sales as $sale)

            <tr class="border-t">

                <td class="p-4">

                    <img
                        src="{{ Storage::url($sale->product->image_path) }}"
                        class="w-16 h-16 rounded object-cover"
                    >

                </td>

                <td class="p-4">

                    {{ $sale->product->name }}

                </td>

                <td class="p-4">

                    {{ $sale->product->category->name }}

                </td>

                <td class="p-4">

                    {{ $sale->buyer->name }}

                </td>

                <td class="p-4">

                    {{ $sale->seller->name }}

                </td>

                <td class="p-4 text-green-600 font-semibold">

                    R$ {{ number_format($sale->price, 2, ',', '.') }}

                </td>

                <td class="p-4">

                    {{ $sale->created_at->format('d/m/Y') }}

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="7" class="text-center py-10 text-gray-500">

                    Nenhuma venda encontrada.

                </td>

            </tr>

            @endforelse

            </tbody>
        </table>

    </div>

    <div class="mt-8 flex justify-center">

        {{ $sales->links() }}

    </div>

</div>

@endsection
