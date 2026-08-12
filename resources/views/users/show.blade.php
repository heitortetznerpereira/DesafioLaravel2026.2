
@extends('layouts.main')

@section('title', 'Visualizar Usuário')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-3xl font-bold mb-8">

        Visualizar Usuário

    </h1>

        @include('users._form', ['readonly' => true])
</div>

@endsection
