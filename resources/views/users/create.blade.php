@extends('layouts.main')

@section('title', 'Novo Usuário')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-3xl font-bold mb-8">

        Novo Usuário

    </h1>

    <form
        action="{{ route('users.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        @include('users._form')

    </form>

</div>

@endsection
