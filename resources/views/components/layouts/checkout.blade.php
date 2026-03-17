@props(['title' => 'Checkout — TakeTicket'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">TakeTicket</a>
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    Secure Checkout
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">
            &copy; {{ date('Y') }} TakeTicket. All rights reserved.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
