<x-layouts.dashboard header="Participantes">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 4px;">Participantes</h2>
            <p style="font-size:13.5px;color:#64748b;margin:0;">Todos os participantes de todos os seus eventos.</p>
        </div>
        <a href="{{ route('dashboard.participantes.export', array_merge(['event_id' => request('event_id')], request()->only('search'))) }}"
           style="display:inline-flex;align-items:center;gap:7px;background:#16a34a;color:white;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exportar CSV
        </a>
    </div>

    {{-- Stat Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p style="font-size:12px;font-weight:500;color:#94a3b8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.5px;">Total</p>
                <p style="font-size:26px;font-weight:800;color:#0f172a;margin:0;line-height:1;">{{ $totalParticipants }}</p>
            </div>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p style="font-size:12px;font-weight:500;color:#94a3b8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.5px;">Check-ins</p>
                <p style="font-size:26px;font-weight:800;color:#16a34a;margin:0;line-height:1;">{{ $checkedInCount }}</p>
            </div>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p style="font-size:12px;font-weight:500;color:#94a3b8;margin:0 0 3px;text-transform:uppercase;letter-spacing:0.5px;">Eventos</p>
                <p style="font-size:26px;font-weight:800;color:#2563eb;margin:0;line-height:1;">{{ $eventsCount }}</p>
            </div>
        </div>

    </div>

    @php
        $selectedEventTitle = '';
        if (request('event_id')) {
            $selectedEventTitle = $events->firstWhere('id', request('event_id'))?->title ?? '';
        }
    @endphp

    {{-- Filters --}}
    <form id="search-form" method="GET" action="{{ route('dashboard.participantes') }}"
          style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px;">

        <input id="search-input" type="text" name="search" value="{{ request('search') }}"
               placeholder="Buscar por nome, e-mail ou CPF..."
               autocomplete="off"
               style="border:1px solid #e2e8f0;border-radius:9px;padding:8px 14px;font-size:13px;color:#334155;outline:none;width:260px;"/>

        {{-- Event combobox --}}
        <div id="event-combo" style="position:relative;">
            <div style="position:relative;display:flex;align-items:center;">
                <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"
                     style="position:absolute;left:11px;pointer-events:none;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <input id="event-combo-text"
                       type="text"
                       placeholder="Todos os eventos"
                       autocomplete="off"
                       value="{{ $selectedEventTitle }}"
                       style="border:1px solid #e2e8f0;border-radius:9px;padding:8px 32px 8px 32px;font-size:13px;color:#334155;outline:none;width:220px;"/>
                <button id="event-combo-clear" type="button"
                        style="position:absolute;right:10px;background:none;border:none;cursor:pointer;padding:0;display:{{ $selectedEventTitle ? 'flex' : 'none' }};align-items:center;color:#94a3b8;"
                        title="Limpar filtro de evento">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <input type="hidden" id="event-combo-value" name="event_id" value="{{ request('event_id') }}"/>
            <div id="event-combo-dropdown"
                 style="display:none;position:absolute;top:calc(100% + 5px);left:0;min-width:100%;width:max-content;max-width:320px;background:white;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.1);z-index:200;max-height:240px;overflow-y:auto;">
            </div>
        </div>

        @if(request('search') || request('event_id'))
            <a href="{{ route('dashboard.participantes') }}" style="font-size:13px;color:#94a3b8;text-decoration:none;">Limpar tudo</a>
        @endif
    </form>

    @push('scripts')
    <script>
    (function () {
        var form      = document.getElementById('search-form');
        var input     = document.getElementById('search-input');
        var container = document.getElementById('results-container');
        var timer;

        // ── combobox state ──────────────────────────────────────────
        var events = @json($events->map(fn($e) => ['id' => $e->id, 'title' => $e->title]));
        var comboText  = document.getElementById('event-combo-text');
        var comboValue = document.getElementById('event-combo-value');
        var comboClear = document.getElementById('event-combo-clear');
        var comboDropdown = document.getElementById('event-combo-dropdown');
        var comboOpen  = false;

        function renderDropdown(filter) {
            var q = (filter || '').toLowerCase();
            var matched = events.filter(function (e) {
                return !q || e.title.toLowerCase().includes(q);
            });
            if (!matched.length) {
                comboDropdown.innerHTML = '<div style="padding:12px 14px;font-size:13px;color:#94a3b8;">Nenhum evento encontrado</div>';
                return;
            }
            comboDropdown.innerHTML = matched.map(function (e) {
                var selected = String(e.id) === String(comboValue.value);
                return '<div data-id="' + e.id + '" data-title="' + e.title.replace(/"/g, '&quot;') + '" ' +
                    'style="padding:10px 14px;font-size:13px;cursor:pointer;color:' + (selected ? '#4f46e5' : '#334155') + ';' +
                    'font-weight:' + (selected ? '600' : '400') + ';' +
                    'border-bottom:1px solid #f8fafc;" ' +
                    'class="combo-option">' + e.title + '</div>';
            }).join('');

            comboDropdown.querySelectorAll('.combo-option').forEach(function (el) {
                el.addEventListener('mouseenter', function () { this.style.background = '#f8fafc'; });
                el.addEventListener('mouseleave', function () { this.style.background = 'white'; });
                el.addEventListener('mousedown', function (ev) {
                    ev.preventDefault(); // prevent blur before click
                    selectEvent(this.dataset.id, this.dataset.title);
                });
            });
        }

        function openDropdown() {
            renderDropdown(comboText.value);
            comboDropdown.style.display = 'block';
            comboOpen = true;
        }

        function closeDropdown() {
            comboDropdown.style.display = 'none';
            comboOpen = false;
            // if text doesn't match the selected value, restore or clear
            if (!comboValue.value) {
                comboText.value = '';
            } else {
                var ev = events.find(function(e){ return String(e.id) === String(comboValue.value); });
                if (ev) comboText.value = ev.title;
            }
        }

        function selectEvent(id, title) {
            comboValue.value = id;
            comboText.value  = title;
            comboClear.style.display = 'flex';
            closeDropdown();
            clearTimeout(timer);
            doSearch();
        }

        comboText.addEventListener('focus', openDropdown);
        comboText.addEventListener('input', function () {
            renderDropdown(this.value);
            if (!comboOpen) { comboDropdown.style.display = 'block'; comboOpen = true; }
            // clear hidden value while typing
            comboValue.value = '';
            comboClear.style.display = 'none';
        });
        comboText.addEventListener('blur', function () {
            setTimeout(closeDropdown, 150);
        });

        comboClear.addEventListener('click', function () {
            comboValue.value = '';
            comboText.value  = '';
            comboClear.style.display = 'none';
            clearTimeout(timer);
            doSearch();
        });

        // ── search fetch ────────────────────────────────────────────
        function doSearch() {
            var params = new URLSearchParams(new FormData(form));
            container.style.opacity = '0.5';
            fetch(form.action + '?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var fresh = doc.getElementById('results-container');
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

    {{-- Table --}}
    <div id="results-container" style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;transition:opacity 0.15s;">

        @if($participants->isEmpty())
            <div style="text-align:center;padding:64px 24px;">
                <div style="width:56px;height:56px;border-radius:16px;background:#ede9fe;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="28" height="28" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Nenhum participante encontrado</p>
                <p style="font-size:13px;color:#94a3b8;margin:0;">Os participantes aparecerão aqui após a compra de ingressos.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Participante</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Documento</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Evento</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Ingresso</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Check-in</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $avatarColors = [
                                'linear-gradient(135deg,#4f46e5,#7c3aed)',
                                'linear-gradient(135deg,#059669,#0d9488)',
                                'linear-gradient(135deg,#ea580c,#db2777)',
                                'linear-gradient(135deg,#0284c7,#4f46e5)',
                                'linear-gradient(135deg,#dc2626,#ea580c)',
                                'linear-gradient(135deg,#7c3aed,#6d28d9)',
                                'linear-gradient(135deg,#0891b2,#0284c7)',
                                'linear-gradient(135deg,#ca8a04,#d97706)',
                            ];
                        @endphp
                        @foreach($participants as $participant)
                            @php
                                $avatarGrad = $avatarColors[$participant->id % 8];
                                $initials = strtoupper(substr($participant->name, 0, 1)) . strtoupper(substr(strstr($participant->name, ' ') ?: ' x', 1, 1));
                                $checkedIn = $participant->ticket?->checked_in_at;
                            @endphp
                            <tr style="border-bottom:1px solid #f8fafc;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                <td style="padding:14px 20px;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:36px;height:36px;border-radius:50%;background:{{ $avatarGrad }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0;">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <p style="font-weight:700;color:#0f172a;margin:0 0 2px;font-size:13.5px;">{{ $participant->name }}</p>
                                            <p style="font-size:12px;color:#94a3b8;margin:0;">{{ $participant->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:14px 20px;color:#64748b;font-size:13px;">
                                    {{ $participant->document ?? '—' }}
                                </td>
                                <td style="padding:14px 20px;max-width:180px;">
                                    <p style="font-weight:600;color:#334155;margin:0 0 2px;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $participant->ticket?->event?->title ?? '—' }}
                                    </p>
                                </td>
                                <td style="padding:14px 20px;">
                                    @if($participant->ticket?->ticketType?->name)
                                        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:100px;font-size:12px;font-weight:600;background:#ede9fe;color:#6d28d9;">
                                            {{ $participant->ticket->ticketType->name }}
                                        </span>
                                    @else
                                        <span style="color:#94a3b8;font-size:13px;">—</span>
                                    @endif
                                </td>
                                <td style="padding:14px 20px;">
                                    @if($checkedIn)
                                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#16a34a;">
                                            <span style="width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
                                            {{ $checkedIn->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#94a3b8;">
                                            <span style="width:7px;height:7px;border-radius:50%;background:#cbd5e1;display:inline-block;"></span>
                                            Pendente
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
                {{ $participants->withQueryString()->links() }}
            </div>
        @endif
    </div>

</x-layouts.dashboard>
