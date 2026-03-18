@props(['event' => null])
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event ? $event->title . ' — Check-in' : 'Staff — TakeTicket' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0;padding:0;min-height:100vh;font-family:'Instrument Sans',sans-serif;background:#f9fafb;">

    {{-- Top header --}}
    <header style="background:white;border-bottom:1px solid #e5e7eb;position:sticky;top:0;z-index:50;">
        <div style="max-width:960px;margin:0 auto;padding:0 16px;height:56px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:16px;">
                <a href="/staff" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
                    <div style="width:32px;height:32px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <span style="font-size:16px;font-weight:800;color:#4f46e5;">TakeTicket</span>
                </a>
                @if($event)
                    <span style="color:#d1d5db;">|</span>
                    <span style="font-size:14px;font-weight:600;color:#374151;">{{ $event->title }}</span>
                    <span style="font-size:13px;color:#9ca3af;">{{ $event->start_date->format('d/m/Y') }}</span>
                @endif
            </div>

            <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:14px;color:#374151;font-weight:500;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" style="font-size:13px;color:#6b7280;background:none;border:1px solid #e5e7eb;border-radius:6px;padding:6px 12px;cursor:pointer;font-family:inherit;">Sair</button>
                </form>
            </div>
        </div>

        {{-- Nav tabs (only when event is set) --}}
        @if($event)
            <div style="max-width:960px;margin:0 auto;padding:0 16px;display:flex;gap:0;border-top:1px solid #f3f4f6;">
                <a href="{{ route('staff.checkin', $event) }}"
                   style="padding:10px 16px;font-size:14px;font-weight:600;text-decoration:none;border-bottom:2px solid {{ request()->routeIs('staff.checkin') ? '#4f46e5' : 'transparent' }};color:{{ request()->routeIs('staff.checkin') ? '#4f46e5' : '#6b7280' }};">
                    Check-in
                </a>
                <a href="{{ route('staff.participants', $event) }}"
                   style="padding:10px 16px;font-size:14px;font-weight:600;text-decoration:none;border-bottom:2px solid {{ request()->routeIs('staff.participants') ? '#4f46e5' : 'transparent' }};color:{{ request()->routeIs('staff.participants') ? '#4f46e5' : '#6b7280' }};">
                    Participantes
                </a>
            </div>
        @endif
    </header>

    <main style="max-width:960px;margin:0 auto;padding:32px 16px;">
        {{ $slot }}
    </main>
</body>
</html>
