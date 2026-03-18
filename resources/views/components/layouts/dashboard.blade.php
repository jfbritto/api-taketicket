@props(['header' => 'Dashboard', 'title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — TakeTicket</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background:#f8fafc;font-family:'Instrument Sans',sans-serif;" x-data="{ sidebarOpen: false }">
<div style="display:flex;height:100vh;overflow:hidden;">

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside style="width:220px;background:#0f172a;display:flex;flex-direction:column;flex-shrink:0;position:relative;z-index:30;"
           class="hidden lg:flex">

        {{-- Logo --}}
        <div style="display:flex;align-items:center;gap:10px;padding:0 20px;height:56px;border-bottom:1px solid rgba(255,255,255,0.06);">
            <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="15" height="15" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <span style="font-size:16px;font-weight:800;color:white;letter-spacing:-0.3px;">TakeTicket</span>
        </div>

        {{-- Nav --}}
        <nav style="flex:1;padding:12px 10px;display:flex;flex-direction:column;gap:2px;overflow-y:auto;">
            @php
                $link = function(string $url, string $label, string $icon, bool $active): string {
                    $base = 'display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;font-size:13.5px;font-weight:500;text-decoration:none;transition:background 0.15s;';
                    $style = $active
                        ? $base . 'background:rgba(255,255,255,0.1);color:white;'
                        : $base . 'color:rgba(148,163,184,1);';
                    return $style;
                };
            @endphp

            @php $isDash = request()->is('dashboard') && !request()->is('dashboard/*'); @endphp
            <a href="{{ url('/dashboard') }}" style="{{ $link(url('/dashboard'), 'Dashboard', '', $isDash) }}"
               onmouseover="if(!{{ $isDash ? 'true' : 'false' }})this.style.background='rgba(255,255,255,0.05)';this.style.color='white'"
               onmouseout="if(!{{ $isDash ? 'true' : 'false' }})this.style.background='transparent';this.style.color='rgba(148,163,184,1)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0h4"/>
                </svg>
                Dashboard
            </a>

            @php $isEvents = request()->is('dashboard/events*'); @endphp
            <a href="{{ url('/dashboard/events') }}" style="{{ $link(url('/dashboard/events'), 'Eventos', '', $isEvents) }}"
               onmouseover="if(!{{ $isEvents ? 'true' : 'false' }})this.style.background='rgba(255,255,255,0.05)';this.style.color='white'"
               onmouseout="if(!{{ $isEvents ? 'true' : 'false' }})this.style.background='transparent';this.style.color='rgba(148,163,184,1)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Eventos
            </a>

            @php $isCheckin = request()->is('dashboard/checkin*'); @endphp
            <a href="{{ url('/dashboard/checkin') }}" style="{{ $link(url('/dashboard/checkin'), 'Check-in', '', $isCheckin) }}"
               onmouseover="if(!{{ $isCheckin ? 'true' : 'false' }})this.style.background='rgba(255,255,255,0.05)';this.style.color='white'"
               onmouseout="if(!{{ $isCheckin ? 'true' : 'false' }})this.style.background='transparent';this.style.color='rgba(148,163,184,1)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Check-in
            </a>

            @php $isFinanceiro = request()->is('dashboard/financeiro*'); @endphp
            <a href="{{ url('/dashboard/financeiro') }}" style="{{ $link(url('/dashboard/financeiro'), 'Financeiro', '', $isFinanceiro) }}"
               onmouseover="if(!{{ $isFinanceiro ? 'true' : 'false' }})this.style.background='rgba(255,255,255,0.05)';this.style.color='white'"
               onmouseout="if(!{{ $isFinanceiro ? 'true' : 'false' }})this.style.background='transparent';this.style.color='rgba(148,163,184,1)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Financeiro
            </a>

            @php $isParticipantes = request()->is('dashboard/participantes*'); @endphp
            <a href="{{ url('/dashboard/participantes') }}" style="{{ $link(url('/dashboard/participantes'), 'Participantes', '', $isParticipantes) }}"
               onmouseover="if(!{{ $isParticipantes ? 'true' : 'false' }})this.style.background='rgba(255,255,255,0.05)';this.style.color='white'"
               onmouseout="if(!{{ $isParticipantes ? 'true' : 'false' }})this.style.background='transparent';this.style.color='rgba(148,163,184,1)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Participantes
            </a>

            <div style="border-top:1px solid rgba(255,255,255,0.06);margin:8px 0;"></div>

            @php $isSettings = request()->is('dashboard/settings*'); @endphp
            <a href="{{ url('/dashboard/settings') }}" style="{{ $link(url('/dashboard/settings'), 'Configurações', '', $isSettings) }}"
               onmouseover="if(!{{ $isSettings ? 'true' : 'false' }})this.style.background='rgba(255,255,255,0.05)';this.style.color='white'"
               onmouseout="if(!{{ $isSettings ? 'true' : 'false' }})this.style.background='transparent';this.style.color='rgba(148,163,184,1)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Configurações
            </a>
        </nav>

        {{-- User + logout --}}
        <div style="border-top:1px solid rgba(255,255,255,0.06);padding:12px 10px;">
            <div style="display:flex;align-items:center;gap:10px;padding:8px 12px;margin-bottom:4px;">
                <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                </div>
                <div style="min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()?->name }}</div>
                    <div style="font-size:11px;color:rgba(148,163,184,1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()?->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button type="submit" style="display:flex;align-items:center;gap:10px;width:100%;padding:9px 12px;border-radius:8px;font-size:13.5px;font-weight:500;color:rgba(148,163,184,1);background:transparent;border:none;cursor:pointer;font-family:inherit;transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='white'"
                        onmouseout="this.style.background='transparent';this.style.color='rgba(148,163,184,1)'">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sair
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main ────────────────────────────────────────────────────────────── --}}
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;">

        {{-- Topbar --}}
        <header style="background:white;border-bottom:1px solid #f1f5f9;height:56px;display:flex;align-items:center;padding:0 24px;flex-shrink:0;">
            <h1 style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">{{ $header }}</h1>
            <div style="margin-left:auto;font-size:13px;color:#94a3b8;">{{ auth()->user()?->name }}</div>
        </header>

        <main style="flex:1;overflow-y:auto;padding:24px;">
            {{ $slot }}
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true,
        didOpen:(t)=>{ t.onmouseenter=Swal.stopTimer; t.onmouseleave=Swal.resumeTimer; }
    }).fire({ icon:'success', title: @json(session('success')) });
</script>
@endif

@if(session('error'))
<script>
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:5000, timerProgressBar:true,
        didOpen:(t)=>{ t.onmouseenter=Swal.stopTimer; t.onmouseleave=Swal.resumeTimer; }
    }).fire({ icon:'error', title: @json(session('error')) });
</script>
@endif

@stack('scripts')
</body>
</html>
