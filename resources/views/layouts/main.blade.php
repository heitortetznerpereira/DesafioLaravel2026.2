<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'Marketplace')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
	<div class="min-h-screen lg:flex">
		@include('layouts.sidebar')

		<main class="min-w-0 flex-1">
			@yield('content')
		</main>
	</div>
</body>
</html>
