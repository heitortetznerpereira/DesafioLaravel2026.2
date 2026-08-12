@extends('layouts.main')

@section('title', 'Editar Admin')

@section('content')

<div class="max-w-3xl mx-auto py-8">

    <h1 class="text-3xl font-bold mb-8">
        Editar Admin
    </h1>

    <form
        action="{{ route('users.update', $user) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @method('PUT')


        @csrf

        @include('admins._form')

    </form>

</div>

@endsection
