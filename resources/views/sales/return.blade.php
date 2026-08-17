@extends('layouts.main')

@section('title', 'Pagamento')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-2xl font-bold">
        Pagamento
    </h1>

    @if ($sale->status === 'paid')
        <p class="mt-4 text-green-600">
            Pagamento aprovado!
        </p>
    @elseif ($sale->status === 'cancelled')
        <p class="mt-4 text-red-600">
            Pagamento cancelado ou recusado.
        </p>
    @else
        <p class="mt-4">
            Seu pagamento está sendo processado.
        </p>
    @endif

</div>

@endsection
