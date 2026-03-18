<x-layouts.dashboard header="Eventos">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 4px;">Meus Eventos</h2>
            <p style="font-size:13.5px;color:#64748b;margin:0;">Gerencie e acompanhe todos os seus eventos.</p>
        </div>
        <a href="{{ route('dashboard.events.create') }}"
           style="display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Criar Evento
        </a>
    </div>

    {{-- Stat Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p style="font-size:12px;font-weight:500;color:#94a3b8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.5px;">Total</p>
                <p style="font-size:26px;font-weight:800;color:#0f172a;margin:0;line-height:1;">{{ $totalEvents }}</p>
            </div>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p style="font-size:12px;font-weight:500;color:#94a3b8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.5px;">Publicados</p>
                <p style="font-size:26px;font-weight:800;color:#16a34a;margin:0;line-height:1;">{{ $publishedCount }}</p>
            </div>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p style="font-size:12px;font-weight:500;color:#94a3b8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.5px;">Próximos</p>
                <p style="font-size:26px;font-weight:800;color:#2563eb;margin:0;line-height:1;">{{ $upcomingCount }}</p>
            </div>
        </div>

    </div>

    {{-- Filter Bar --}}
    <form id="events-search-form" method="GET" action="{{ route('dashboard.events') }}"
          style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;">

        {{-- Text search --}}
        <div style="position:relative;display:flex;align-items:center;">
            <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"
                 style="position:absolute;left:11px;pointer-events:none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
            </svg>
            <input id="events-search-input" type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por nome ou local..."
                   autocomplete="off"
                   style="border:1px solid #e2e8f0;border-radius:9px;padding:8px 14px 8px 32px;font-size:13px;color:#334155;outline:none;width:240px;"/>
        </div>

        {{-- Status combobox --}}
        @php
            $statusLabelsFilter = ['draft' => 'Rascunho', 'published' => 'Publicado', 'cancelled' => 'Cancelado'];
            $selectedStatusLabel = request('status') ? ($statusLabelsFilter[request('status')] ?? '') : '';
        @endphp
        <div id="status-combo" style="position:relative;">
            <div style="position:relative;display:flex;align-items:center;">
                <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"
                     style="position:absolute;left:11px;pointer-events:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 8h10M11 12h4"/>
                </svg>
                <input id="status-combo-text"
                       type="text"
                       placeholder="Todos os status"
                       autocomplete="off"
                       value="{{ $selectedStatusLabel }}"
                       style="border:1px solid #e2e8f0;border-radius:9px;padding:8px 32px 8px 32px;font-size:13px;color:#334155;outline:none;width:180px;"/>
                <button id="status-combo-clear" type="button"
                        style="position:absolute;right:10px;background:none;border:none;cursor:pointer;padding:0;display:{{ $selectedStatusLabel ? 'flex' : 'none' }};align-items:center;color:#94a3b8;"
                        title="Limpar filtro">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <input type="hidden" id="status-combo-value" name="status" value="{{ request('status') }}"/>
            <div id="status-combo-dropdown"
                 style="display:none;position:absolute;top:calc(100% + 5px);left:0;width:180px;background:white;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.1);z-index:200;">
            </div>
        </div>

        @if(request('search') || request('status'))
            <a href="{{ route('dashboard.events') }}" style="font-size:13px;color:#94a3b8;text-decoration:none;">Limpar tudo</a>
        @endif
    </form>

    @push('scripts')
    <script>
    (function () {
        var form      = document.getElementById('events-search-form');
        var input     = document.getElementById('events-search-input');
        var container = document.getElementById('events-results-container');
        var timer;

        // ── status combobox ──────────────────────────────────────────
        var statusOptions = [
            { value: 'draft',     label: 'Rascunho' },
            { value: 'published', label: 'Publicado' },
            { value: 'cancelled', label: 'Cancelado' },
        ];
        var statusText     = document.getElementById('status-combo-text');
        var statusValue    = document.getElementById('status-combo-value');
        var statusClear    = document.getElementById('status-combo-clear');
        var statusDropdown = document.getElementById('status-combo-dropdown');

        function renderStatusDropdown(filter) {
            var q = (filter || '').toLowerCase();
            var matched = statusOptions.filter(function (o) {
                return !q || o.label.toLowerCase().includes(q);
            });
            if (!matched.length) {
                statusDropdown.innerHTML = '<div style="padding:10px 14px;font-size:13px;color:#94a3b8;">Nenhum resultado</div>';
                return;
            }
            statusDropdown.innerHTML = matched.map(function (o) {
                var sel = o.value === statusValue.value;
                return '<div data-value="' + o.value + '" data-label="' + o.label + '" class="status-option" ' +
                    'style="padding:10px 14px;font-size:13px;cursor:pointer;color:' + (sel ? '#4f46e5' : '#334155') + ';' +
                    'font-weight:' + (sel ? '600' : '400') + ';border-bottom:1px solid #f8fafc;">' +
                    o.label + '</div>';
            }).join('');

            statusDropdown.querySelectorAll('.status-option').forEach(function (el) {
                el.addEventListener('mouseenter', function () { this.style.background = '#f8fafc'; });
                el.addEventListener('mouseleave', function () { this.style.background = 'white'; });
                el.addEventListener('mousedown', function (ev) {
                    ev.preventDefault();
                    statusValue.value = this.dataset.value;
                    statusText.value  = this.dataset.label;
                    statusClear.style.display = 'flex';
                    statusDropdown.style.display = 'none';
                    clearTimeout(timer);
                    doSearch();
                });
            });
        }

        statusText.addEventListener('focus', function () {
            renderStatusDropdown(this.value);
            statusDropdown.style.display = 'block';
        });
        statusText.addEventListener('input', function () {
            renderStatusDropdown(this.value);
            statusDropdown.style.display = 'block';
            statusValue.value = '';
            statusClear.style.display = 'none';
        });
        statusText.addEventListener('blur', function () {
            setTimeout(function () {
                statusDropdown.style.display = 'none';
                if (!statusValue.value) statusText.value = '';
                else {
                    var opt = statusOptions.find(function(o){ return o.value === statusValue.value; });
                    if (opt) statusText.value = opt.label;
                }
            }, 150);
        });
        statusClear.addEventListener('click', function () {
            statusValue.value = '';
            statusText.value  = '';
            statusClear.style.display = 'none';
            clearTimeout(timer);
            doSearch();
        });

        // ── fetch ────────────────────────────────────────────────────
        function doSearch() {
            var params = new URLSearchParams(new FormData(form));
            container.style.opacity = '0.5';
            fetch(form.action + '?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var fresh = doc.getElementById('events-results-container');
                if (fresh) container.innerHTML = fresh.innerHTML;
                container.style.opacity = '1';
                history.replaceState({}, '', form.action + '?' + params.toString());
            })
            .catch(function () { container.style.opacity = '1'; });
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(doSearch, 400);
        });
    })();
    </script>
    @endpush

    {{-- Events Table --}}
    <div id="events-results-container" style="transition:opacity 0.15s;">
    <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;">

        @if($events->isEmpty())
            <div style="text-align:center;padding:64px 24px;">
                <div style="width:56px;height:56px;border-radius:16px;background:#ede9fe;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="28" height="28" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Nenhum evento ainda</p>
                <p style="font-size:13px;color:#94a3b8;margin:0 0 20px;">Crie seu primeiro evento e comece a vender ingressos.</p>
                <a href="{{ route('dashboard.events.create') }}"
                   style="display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;">
                    Criar meu primeiro evento
                </a>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Evento</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Data</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Vendidos</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusLabels = ['draft' => 'Rascunho', 'published' => 'Publicado', 'cancelled' => 'Cancelado', 'finished' => 'Encerrado'];
                            $gradients = [
                                'linear-gradient(135deg,#4f46e5,#7c3aed)',
                                'linear-gradient(135deg,#059669,#0d9488)',
                                'linear-gradient(135deg,#ea580c,#db2777)',
                                'linear-gradient(135deg,#0284c7,#4f46e5)',
                                'linear-gradient(135deg,#dc2626,#ea580c)',
                                'linear-gradient(135deg,#7c3aed,#6d28d9)',
                            ];
                        @endphp
                        @foreach($events as $event)
                            @php
                                $grad = $gradients[$event->id % 6];
                                $statusValue = $event->status->value;
                                $statusDotColors = ['published' => '#16a34a', 'draft' => '#94a3b8', 'cancelled' => '#dc2626', 'finished' => '#2563eb'];
                                $dotColor = $statusDotColors[$statusValue] ?? '#94a3b8';
                            @endphp
                            <tr style="border-bottom:1px solid #f8fafc;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                <td style="padding:14px 20px;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:40px;height:40px;border-radius:10px;background:{{ $grad }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p style="font-weight:700;color:#0f172a;margin:0 0 2px;font-size:14px;">{{ $event->title }}</p>
                                            <p style="font-size:12px;color:#94a3b8;margin:0;">
                                                @if($event->city || $event->state)
                                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-1px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    {{ $event->city }}{{ $event->city && $event->state ? ', ' : '' }}{{ $event->state }}
                                                @else
                                                    Sem localização
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:14px 20px;color:#475569;font-size:13px;">
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <svg width="13" height="13" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $event->start_date->format('d/m/Y') }}<br>
                                        <span style="color:#94a3b8;font-size:12px;">{{ $event->start_date->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td style="padding:14px 20px;">
                                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#334155;">
                                        <span style="width:7px;height:7px;border-radius:50%;background:{{ $dotColor }};display:inline-block;"></span>
                                        {{ $statusLabels[$statusValue] ?? $statusValue }}
                                    </span>
                                </td>
                                <td style="padding:14px 20px;">
                                    <span style="font-size:18px;font-weight:800;color:#0f172a;">{{ $event->orders_count ?? 0 }}</span>
                                    <span style="font-size:12px;color:#94a3b8;margin-left:3px;">ingresso(s)</span>
                                </td>
                                <td style="padding:14px 20px;">
                                    <a href="{{ route('dashboard.events.show', $event) }}"
                                       style="color:#4f46e5;font-weight:600;font-size:13px;text-decoration:none;">
                                        Gerenciar →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
                {{ $events->withQueryString()->links() }}
            </div>
        @endif
    </div>
    </div>{{-- #events-results-container --}}

</x-layouts.dashboard>
