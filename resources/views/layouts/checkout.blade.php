<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Checkout' }} - TakeTicket</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">TakeTicket</a>
            <span class="text-sm text-gray-500">Secure Checkout</span>
        </div>
    </nav>

    @if(session('error'))
        <div class="max-w-4xl mx-auto px-4 mt-4">
            <x-alert type="error" :message="session('error')" />
        </div>
    @endif

    <main class="max-w-4xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
