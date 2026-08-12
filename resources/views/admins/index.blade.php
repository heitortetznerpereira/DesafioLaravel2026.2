@extends('layouts.main')

@section('title', 'Admins')

@section('content')

<div class="max-w-7xl mx-auto py-8">

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">
            Admins
        </h1>

        <a
            href="{{ route('users.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg"
        >
            Novo Admin
        </a>

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

                    <th class="p-4">Foto</th>

                    <th class="p-4">Nome</th>

                    <th class="p-4">Email</th>

                    <th class="p-4">CPF</th>

                    <th class="p-4">Ações</th>

                </tr>

            </thead>

            <tbody>

                @forelse($users as $user)

                    <tr class="border-t">

                        <td class="p-4">

                            <img
                                src="{{ Storage::url($user->image) }}"
                                class="w-20 h-20 object-cover rounded"
                            >

                        </td>

                        <td class="p-4">

                            {{ $user->name }}

                        </td>

                        <td class="p-4">

                            {{ $user->email}}

                        </td>

                        <td class="p-4">

                            {{ $user->cpf }}

                        </td>

                        <td class="p-4">

                            <div class="flex gap-2">

                                <a
                                    href="{{ route('users.show', $user) }}"
                                    class="bg-green-600 text-white px-4 py-2 rounded"
                                >
                                    Ver
                                </a>

                                <a
                                    href="{{ route('users.edit', $user) }}"
                                    class="bg-yellow-500 text-white px-4 py-2 rounded"
                                >
                                    Editar
                                </a>

                                <form
                                    action="{{ route('users.destroy', $user) }}"
                                    method="POST"
                                    onsubmit="return confirm('Deseja excluir este usuário?')"
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

                            Nenhum admin cadastrado.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-8">

        {{ $users->links() }}

    </div>

</div>

@endsection
