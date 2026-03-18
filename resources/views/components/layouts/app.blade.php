@props(['title' => 'TakeTicket'])
<!DOCTYPE html>
<html lang="pt-BR">
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
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-40" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 flex-shrink-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">TakeTicket</span>
                </a>

                {{-- Desktop links --}}
                <div class="hidden md:flex items-center gap-1">
                    @auth
                        <a href="{{ url('/my-tickets') }}"
                           class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium transition
                                  {{ request()->is('my-tickets*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                            Meus Ingressos
                        </a>
                        <a href="{{ url('/dashboard') }}"
                           class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium transition
                                  {{ request()->is('dashboard*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Painel
                        </a>

                        {{-- User dropdown --}}
                        <div class="relative ml-2" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 pl-3 pr-2.5 py-1.5 rounded-xl border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition text-sm font-medium text-gray-700">
                                <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                {{ explode(' ', auth()->user()->name)[0] }}
                                <svg class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-44 bg-white rounded-2xl shadow-lg border border-gray-100 py-1.5 z-50">
                                <form method="POST" action="{{ url('/logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ url('/login') }}"
                           class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition">
                            Entrar
                        </a>
                        <a href="{{ url('/register') }}"
                           class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:opacity-90 transition shadow-sm">
                            Cadastrar-se
                        </a>
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-xl text-gray-500 hover:bg-gray-100 transition">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            @auth
                <a href="{{ url('/my-tickets') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    Meus Ingressos
                </a>
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Painel
                </a>
                <div class="border-t border-gray-100 pt-2 mt-2">
                    <div class="flex items-center gap-2.5 px-3 py-2 text-sm text-gray-500 mb-1">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        {{ auth()->user()->name }}
                    </div>
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sair
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ url('/login') }}" class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition">Entrar</a>
                <a href="{{ url('/register') }}" class="flex items-center justify-center bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">Cadastrar-se</a>
            @endauth
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

    <footer style="background:#0f172a;margin-top:auto;">

        {{-- Main footer content --}}
        <div style="max-width:1100px;margin:0 auto;padding:56px 24px 40px;display:grid;grid-template-columns:2fr 1.5fr 1fr;gap:48px;">

            {{-- Column 1: Brand + description --}}
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <span style="font-size:17px;font-weight:800;color:white;letter-spacing:-0.3px;">TakeTicket</span>
                </div>
                <p style="font-size:13.5px;color:rgba(148,163,184,0.9);line-height:1.7;margin:0 0 20px;max-width:320px;">
                    Plataforma de venda e gestão de ingressos para eventos de todos os tamanhos. Simples para o organizador, ágil para o participante.
                </p>
                <a href="https://helpflux.com.br/" target="_blank" rel="noopener"
                   style="font-size:12.5px;color:rgba(148,163,184,0.5);text-decoration:none;display:inline-flex;align-items:center;gap:5px;"
                   onmouseover="this.style.color='rgba(148,163,184,0.9)'" onmouseout="this.style.color='rgba(148,163,184,0.5)'">
                    Um produto
                    <span style="color:rgba(148,163,184,0.85);font-weight:700;">HelpFlux</span>
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>

            {{-- Column 2: Company info --}}
            <div>
                <p style="font-size:11.5px;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1px;margin:0 0 16px;">Empresa</p>
                <p style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.85);margin:0 0 8px;line-height:1.4;">HELPFLUX SOLUÇÕES EM TECNOLOGIA LTDA</p>
                <p style="font-size:13px;color:rgba(148,163,184,0.7);margin:0 0 4px;">CNPJ: 58.063.432/0001-21</p>
                <p style="font-size:13px;color:rgba(148,163,184,0.7);margin:0 0 20px;">Santa Maria de Jetibá – ES</p>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="mailto:helpflux.atendimento@gmail.com"
                       style="font-size:13px;color:rgba(148,163,184,0.7);text-decoration:none;display:inline-flex;align-items:center;gap:6px;"
                       onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.7)'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        helpflux.atendimento@gmail.com
                    </a>
                    <a href="tel:+5528999743099"
                       style="font-size:13px;color:rgba(148,163,184,0.7);text-decoration:none;display:inline-flex;align-items:center;gap:6px;"
                       onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.7)'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        (28) 99974-3099
                    </a>
                    <a href="https://helpflux.com.br/" target="_blank" rel="noopener"
                       style="font-size:13px;color:rgba(148,163,184,0.7);text-decoration:none;display:inline-flex;align-items:center;gap:6px;"
                       onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.7)'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                        </svg>
                        helpflux.com.br
                    </a>
                </div>
            </div>

            {{-- Column 3: Navigation --}}
            <div>
                <p style="font-size:11.5px;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1px;margin:0 0 16px;">TakeTicket</p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <a href="{{ route('home') }}"
                       style="font-size:13.5px;color:rgba(148,163,184,0.8);text-decoration:none;"
                       onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.8)'">Explorar Eventos</a>
                    @auth
                        <a href="{{ route('my-tickets') }}"
                           style="font-size:13.5px;color:rgba(148,163,184,0.8);text-decoration:none;"
                           onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.8)'">Meus Ingressos</a>
                        <a href="{{ url('/dashboard') }}"
                           style="font-size:13.5px;color:rgba(148,163,184,0.8);text-decoration:none;"
                           onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.8)'">Painel do Organizador</a>
                    @else
                        <a href="{{ route('login') }}"
                           style="font-size:13.5px;color:rgba(148,163,184,0.8);text-decoration:none;"
                           onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.8)'">Entrar</a>
                        <a href="{{ route('register') }}"
                           style="font-size:13.5px;color:rgba(148,163,184,0.8);text-decoration:none;"
                           onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(148,163,184,0.8)'">Criar conta</a>
                    @endauth
                </div>
            </div>

        </div>

        {{-- Bottom bar --}}
        <div style="border-top:1px solid rgba(255,255,255,0.06);padding:20px 24px;">
            <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <p style="font-size:12.5px;color:rgba(148,163,184,0.5);margin:0;">
                    &copy; {{ date('Y') }} TakeTicket. Todos os direitos reservados.
                </p>
                <div style="display:flex;align-items:center;gap:20px;">
                    <a href="{{ route('terms') }}"
                       style="font-size:12.5px;color:rgba(148,163,184,0.5);text-decoration:none;"
                       onmouseover="this.style.color='rgba(148,163,184,0.9)'" onmouseout="this.style.color='rgba(148,163,184,0.5)'">
                        Termos de Uso
                    </a>
                    <a href="{{ route('privacy') }}"
                       style="font-size:12.5px;color:rgba(148,163,184,0.5);text-decoration:none;"
                       onmouseover="this.style.color='rgba(148,163,184,0.9)'" onmouseout="this.style.color='rgba(148,163,184,0.5)'">
                        Política de Privacidade
                    </a>
                </div>
            </div>
        </div>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/imask@7.6.1/dist/imask.min.js"></script>
    @stack('scripts')
</body>
</html>
