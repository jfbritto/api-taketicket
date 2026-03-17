@props(['title' => 'TakeTicket'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TakeTicket' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    {{-- Navigation --}}
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">TakeTicket</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/my-tickets') }}" class="text-gray-600 hover:text-gray-900">Meus Ingressos</a>
                        <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900">Painel</a>
                        <form method="POST" action="{{ url('/logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">Sair</button>
                        </form>
                    @else
                        <a href="{{ url('/login') }}" class="text-gray-600 hover:text-gray-900">Entrar</a>
                        <a href="{{ url('/register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Cadastrar-se</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{-- Content --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} TakeTicket. Todos os direitos reservados.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/imask@7.6.1/dist/imask.min.js"></script>
    @stack('scripts')
</body>
</html>
