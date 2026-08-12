
@extends('layouts.main')

@section('title', 'Visualizar Admin')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-3xl font-bold mb-8">

        Visualizar Admin

    </h1>

        @include('admins._form', ['readonly' => true])
</div>

@endsection
