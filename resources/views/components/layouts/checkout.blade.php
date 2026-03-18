@props(['title' => 'Checkout — TakeTicket'])
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        * { font-family: 'Instrument Sans', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }
    </style>
</head>
<body>
    {{-- Header --}}
    <nav style="background:white;border-bottom:1px solid #f1f5f9;position:sticky;top:0;z-index:50;">
        <div style="max-width:1100px;margin:0 auto;padding:0 20px;height:60px;display:flex;align-items:center;justify-content:space-between;">
            <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
                <div style="width:32px;height:32px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <span style="font-size:17px;font-weight:800;background:linear-gradient(135deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-0.3px;">TakeTicket</span>
            </a>

            <div style="display:flex;align-items:center;gap:6px;">
                <svg width="14" height="14" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span style="font-size:13px;font-weight:600;color:#16a34a;">Checkout Seguro</span>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <main style="flex:1;">
        {{ $slot }}
    </main>

    <footer style="background:white;border-top:1px solid #f1f5f9;padding:20px;margin-top:auto;">
        <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <p style="font-size:12px;color:#94a3b8;margin:0;">&copy; {{ date('Y') }} TakeTicket · Um produto HelpFlux</p>
            <div style="display:flex;align-items:center;gap:16px;">
                <a href="{{ route('terms') }}" style="font-size:12px;color:#94a3b8;text-decoration:none;" target="_blank">Termos de Uso</a>
                <a href="{{ route('privacy') }}" style="font-size:12px;color:#94a3b8;text-decoration:none;" target="_blank">Privacidade</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
