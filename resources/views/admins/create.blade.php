@extends('layouts.main')

@section('title', 'Novo Admin')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-3xl font-bold mb-8">

        Novo Admin

    </h1>

    <form
        action="{{ route('users.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        @include('admins._form')

    </form>

</div>

@endsection
