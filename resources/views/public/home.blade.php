<x-layouts.app title="TakeTicket — Ingressos para os melhores eventos">

    {{-- ── HERO ─────────────────────────────────────────────────────────────── --}}
    <section style="background:#0f172a;position:relative;overflow:hidden;">

        {{-- Decorative blobs --}}
        <div style="position:absolute;top:-120px;right:-80px;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,0.25) 0%,transparent 70%);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-100px;left:-60px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(79,70,229,0.2) 0%,transparent 70%);pointer-events:none;"></div>
        <div style="position:absolute;top:40%;left:50%;transform:translate(-50%,-50%);width:800px;height:300px;background:radial-gradient(ellipse,rgba(99,102,241,0.12) 0%,transparent 70%);pointer-events:none;"></div>

        <div style="max-width:1100px;margin:0 auto;padding:88px 24px 72px;position:relative;text-align:center;">

            {{-- Pill label --}}
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.3);border-radius:100px;padding:6px 16px;margin-bottom:28px;">
                <span style="width:7px;height:7px;border-radius:50%;background:#818cf8;display:inline-block;animation:pulse 2s infinite;"></span>
                <span style="font-size:13px;font-weight:600;color:#a5b4fc;letter-spacing:0.3px;">Plataforma de ingressos online</span>
            </div>

            {{-- Headline --}}
            <h1 style="font-size:clamp(36px,6vw,64px);font-weight:900;color:white;margin:0 0 20px;line-height:1.1;letter-spacing:-1.5px;font-family:'Instrument Sans',sans-serif;">
                Sua experiência começa<br>
                <span style="background:linear-gradient(135deg,#818cf8,#c084fc,#f472b6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">aqui.</span>
            </h1>

            <p style="font-size:clamp(16px,2vw,20px);color:rgba(148,163,184,0.9);margin:0 auto 48px;max-width:520px;line-height:1.6;">
                Descubra e compre ingressos para os melhores eventos perto de você.
            </p>

            {{-- Search bar --}}
            <form method="GET" action="{{ url('/') }}"
                  style="background:rgba(255,255,255,0.06);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:10px;max-width:680px;margin:0 auto 56px;display:flex;flex-wrap:wrap;gap:8px;">
                <div style="flex:1;min-width:180px;position:relative;display:flex;align-items:center;">
                    <svg width="15" height="15" fill="none" stroke="rgba(148,163,184,0.7)" stroke-width="2" viewBox="0 0 24 24"
                         style="position:absolute;left:12px;pointer-events:none;flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Buscar eventos..."
                           style="width:100%;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:11px 14px 11px 36px;font-size:14px;color:white;outline:none;font-family:'Instrument Sans',sans-serif;"
                           onfocus="this.style.borderColor='rgba(129,140,248,0.5)';this.style.background='rgba(255,255,255,0.1)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.07)'"/>
                </div>
                <div style="width:140px;position:relative;display:flex;align-items:center;">
                    <svg width="14" height="14" fill="none" stroke="rgba(148,163,184,0.7)" stroke-width="2" viewBox="0 0 24 24"
                         style="position:absolute;left:12px;pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <input type="text" name="city" value="{{ request('city') }}"
                           placeholder="Cidade"
                           style="width:100%;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:11px 14px 11px 34px;font-size:14px;color:white;outline:none;font-family:'Instrument Sans',sans-serif;"
                           onfocus="this.style.borderColor='rgba(129,140,248,0.5)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"/>
                </div>
                <button type="submit"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;padding:11px 24px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:'Instrument Sans',sans-serif;letter-spacing:0.2px;">
                    Buscar Eventos
                </button>
            </form>

            {{-- Stats --}}
            <div style="display:inline-flex;align-items:center;gap:32px;padding:16px 32px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:100px;">
                <div style="text-align:center;">
                    <p style="font-size:22px;font-weight:900;color:white;margin:0;line-height:1;">{{ $totalEvents > 0 ? number_format($totalEvents) : '100+' }}</p>
                    <p style="font-size:12px;color:rgba(148,163,184,0.6);margin:3px 0 0;font-weight:500;">Eventos ativos</p>
                </div>
                <div style="width:1px;height:36px;background:rgba(255,255,255,0.1);"></div>
                <div style="text-align:center;">
                    <p style="font-size:22px;font-weight:900;color:white;margin:0;line-height:1;">{{ $totalTickets > 0 ? number_format($totalTickets) : '5.000+' }}</p>
                    <p style="font-size:12px;color:rgba(148,163,184,0.6);margin:3px 0 0;font-weight:500;">Pedidos realizados</p>
                </div>
                <div style="width:1px;height:36px;background:rgba(255,255,255,0.1);"></div>
                <div style="text-align:center;">
                    <p style="font-size:22px;font-weight:900;color:white;margin:0;line-height:1;">100%</p>
                    <p style="font-size:12px;color:rgba(148,163,184,0.6);margin:3px 0 0;font-weight:500;">Digital &amp; seguro</p>
                </div>
            </div>

        </div>
    </section>

    {{-- ── EVENTS ───────────────────────────────────────────────────────────── --}}
    <section style="background:#f8fafc;padding:64px 0;">
        <div style="max-width:1100px;margin:0 auto;padding:0 24px;">

            {{-- Section header --}}
            @if(request('search') || request('city') || request('date_from') || request('date_to'))
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h2 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px;font-family:'Instrument Sans',sans-serif;">Resultados da busca</h2>
                        <p style="font-size:14px;color:#64748b;margin:0;">
                            @if($events->total() > 0)
                                <strong style="color:#4f46e5;">{{ $events->total() }}</strong> evento(s) encontrado(s)
                            @else
                                Nenhum evento encontrado para os filtros aplicados
                            @endif
                        </p>
                    </div>
                    <a href="{{ url('/') }}"
                       style="display:inline-flex;align-items:center;gap:6px;font-size:13.5px;font-weight:600;color:#64748b;text-decoration:none;border:1px solid #e2e8f0;padding:8px 16px;border-radius:9px;background:white;"
                       onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Limpar filtros
                    </a>
                </div>
            @else
                <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:12px;">
                    <div>
                        <p style="font-size:12px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:1px;margin:0 0 6px;">Próximos eventos</p>
                        <h2 style="font-size:26px;font-weight:900;color:#0f172a;margin:0;font-family:'Instrument Sans',sans-serif;">Eventos em Destaque</h2>
                    </div>
                </div>
            @endif

            @if($events->isEmpty())
                <div style="text-align:center;padding:80px 24px;">
                    <div style="width:72px;height:72px;border-radius:20px;background:#ede9fe;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <svg width="32" height="32" fill="none" stroke="#7c3aed" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 8px;">Nenhum evento encontrado</p>
                    <p style="font-size:14px;color:#94a3b8;margin:0;">Tente ajustar os filtros de busca ou volte em breve.</p>
                </div>
            @else
                {{-- Events grid --}}
                <div id="events-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:22px;">
                    @include('public._event-cards', ['events' => $events])
                </div>

                {{-- Infinite scroll sentinel --}}
                @if($events->hasMorePages())
                    <div id="scroll-sentinel" style="height:1px;margin-top:40px;"></div>
                @endif

                {{-- Loading spinner --}}
                <div id="scroll-loader" style="display:none;text-align:center;padding:40px 0;">
                    <div style="display:inline-flex;align-items:center;gap:10px;color:#64748b;font-size:14px;font-weight:500;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.5"
                             style="animation:spin 0.7s linear infinite;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Carregando mais eventos...
                    </div>
                </div>

                {{-- End of results --}}
                <div id="scroll-end" style="display:none;text-align:center;padding:40px 0;">
                    <p style="font-size:13px;color:#94a3b8;margin:0;">Todos os eventos foram carregados.</p>
                </div>

                <script>
                (function () {
                    var page    = 2;
                    var loading = false;
                    var hasMore = {{ $events->hasMorePages() ? 'true' : 'false' }};
                    var grid    = document.getElementById('events-grid');
                    var loader  = document.getElementById('scroll-loader');
                    var endMsg  = document.getElementById('scroll-end');
                    var sentinel= document.getElementById('scroll-sentinel');
                    var baseUrl = '{{ url('/') }}';

                    if (!sentinel) return;

                    function buildUrl() {
                        var params = new URLSearchParams(window.location.search);
                        params.set('page', page);
                        params.set('_json', '1');
                        return baseUrl + '?' + params.toString();
                    }

                    function loadMore() {
                        if (loading || !hasMore) return;
                        loading = true;
                        if (loader) loader.style.display = 'block';

                        fetch(buildUrl())
                            .then(function(r) {
                                if (!r.ok) throw new Error('HTTP ' + r.status);
                                return r.json();
                            })
                            .then(function(data) {
                                var tmp = document.createElement('div');
                                tmp.innerHTML = data.html;
                                while (tmp.firstChild) grid.appendChild(tmp.firstChild);

                                page++;
                                hasMore = data.hasMore;
                                loading = false;
                                if (loader) loader.style.display = 'none';

                                if (!hasMore) {
                                    observer.disconnect();
                                    if (sentinel) sentinel.remove();
                                    if (endMsg) endMsg.style.display = 'block';
                                }
                            })
                            .catch(function(e) {
                                loading = false;
                                if (loader) loader.style.display = 'none';
                                console.error('Infinite scroll error:', e);
                            });
                    }

                    var observer = new IntersectionObserver(function(entries) {
                        if (entries[0].isIntersecting) loadMore();
                    }, { rootMargin: '300px' });

                    observer.observe(sentinel);
                }());
                </script>
            @endif
        </div>
    </section>

    {{-- ── FEATURES (only on homepage, no filters active) ─────────────────── --}}
    @unless(request()->anyFilled(['search','city','date_from','date_to']))
    <section style="background:white;padding:80px 0;border-top:1px solid #f1f5f9;">
        <div style="max-width:1100px;margin:0 auto;padding:0 24px;">

            <div style="text-align:center;margin-bottom:56px;">
                <p style="font-size:12px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:1px;margin:0 0 10px;">Para organizadores</p>
                <h2 style="font-size:32px;font-weight:900;color:#0f172a;margin:0 0 14px;font-family:'Instrument Sans',sans-serif;">Organize. Venda. Gerencie.</h2>
                <p style="font-size:16px;color:#64748b;margin:0 auto;max-width:480px;line-height:1.6;">Tudo que você precisa para criar e gerenciar eventos com profissionalismo.</p>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:48px;">

                <div style="padding:32px 28px;border-radius:20px;border:1px solid #f1f5f9;background:#fafafa;">
                    <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#ede9fe,#dbeafe);display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <svg width="22" height="22" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <h3 style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 10px;font-family:'Instrument Sans',sans-serif;">Venda Online</h3>
                    <p style="font-size:14px;color:#64748b;margin:0;line-height:1.7;">Crie múltiplos tipos de ingressos, defina preços e períodos de venda. Receba pagamentos de forma segura e automática.</p>
                </div>

                <div style="padding:32px 28px;border-radius:20px;border:1px solid #f1f5f9;background:#fafafa;">
                    <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#dcfce7,#dbeafe);display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <svg width="22" height="22" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 10px;font-family:'Instrument Sans',sans-serif;">Check-in Digital</h3>
                    <p style="font-size:14px;color:#64748b;margin:0;line-height:1.7;">Valide ingressos por QR Code em segundos. Delegue o acesso à sua equipe com controle total de permissões.</p>
                </div>

                <div style="padding:32px 28px;border-radius:20px;border:1px solid #f1f5f9;background:#fafafa;">
                    <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#fef9c3,#fee2e2);display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                        <svg width="22" height="22" fill="none" stroke="#ca8a04" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 10px;font-family:'Instrument Sans',sans-serif;">Dashboard Completo</h3>
                    <p style="font-size:14px;color:#64748b;margin:0;line-height:1.7;">Acompanhe vendas, participantes e financeiro em tempo real. Exporte dados e gerencie tudo em um só lugar.</p>
                </div>

            </div>

            {{-- CTA --}}
            <div style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border-radius:24px;padding:56px 48px;text-align:center;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-60px;right:-60px;width:240px;height:240px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,0.2) 0%,transparent 70%);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-60px;left:-60px;width:240px;height:240px;border-radius:50%;background:radial-gradient(circle,rgba(79,70,229,0.2) 0%,transparent 70%);pointer-events:none;"></div>
                <p style="font-size:13px;font-weight:700;color:#818cf8;text-transform:uppercase;letter-spacing:1px;margin:0 0 12px;position:relative;">Gratuito para começar</p>
                <h3 style="font-size:28px;font-weight:900;color:white;margin:0 0 14px;font-family:'Instrument Sans',sans-serif;position:relative;">Pronto para criar seu evento?</h3>
                <p style="font-size:15px;color:rgba(148,163,184,0.8);margin:0 auto 32px;max-width:400px;line-height:1.6;position:relative;">Crie sua conta gratuitamente e comece a vender ingressos em minutos.</p>
                <div style="display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;position:relative;">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;padding:13px 28px;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;font-family:'Instrument Sans',sans-serif;">
                            Acessar Painel
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;padding:13px 28px;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;font-family:'Instrument Sans',sans-serif;">
                            Criar conta grátis
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                           style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.8);padding:13px 24px;border-radius:12px;font-size:15px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,0.12);font-family:'Instrument Sans',sans-serif;">
                            Já tenho conta
                        </a>
                    @endauth
                </div>
            </div>

        </div>
    </section>
    @endunless

    <style>
    @@keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    @@keyframes spin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    input::placeholder { color: rgba(148,163,184,0.6); }
    </style>

</x-layouts.app>
