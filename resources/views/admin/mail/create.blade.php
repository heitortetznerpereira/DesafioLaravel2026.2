@extends('layouts.main')

@section('title', 'Enviar E-mail')

@section('content')

<div class="max-w-3xl mx-auto py-8 px-4">

    <h1 class="text-3xl font-bold mb-8">
        Enviar E-mail
    </h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form
        action="{{ route('admin.mail.store') }}"
        method="POST"
        class="space-y-6"
    >

        @csrf

        <div>
            <label for="user_id" class="block font-semibold mb-2">
                Usuário
            </label>

            <select
                id="user_id"
                name="user_id"
                class="w-full border rounded-lg px-4 py-2"
                required
            >
                <option value="">
                    Selecione um usuário
                </option>

                @foreach($users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }} - {{ $user->email }}
                    </option>
                @endforeach
            </select>

            @error('user_id')
                <p class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="subject" class="block font-semibold mb-2">
                Assunto
            </label>

            <input
                id="subject"
                type="text"
                name="subject"
                value="{{ old('subject') }}"
                class="w-full border rounded-lg px-4 py-2"
                required
            >

            @error('subject')
                <p class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="message" class="block font-semibold mb-2">
                Mensagem
            </label>

            <textarea
                id="message"
                name="message"
                rows="8"
                class="w-full border rounded-lg px-4 py-2"
                required
            >{{ old('message') }}</textarea>

            @error('message')
                <p class="text-red-500 text-sm mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg"
        >
            Enviar E-mail
        </button>

    </form>

</div>

@endsection
