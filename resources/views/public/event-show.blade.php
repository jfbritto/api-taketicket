<x-layouts.app :title="$event->title">

    {{-- Hero Banner --}}
    @if($event->banner)
        <div style="width:100%;height:380px;position:relative;overflow:hidden;">
            <img src="{{ Storage::url($event->banner) }}" alt="{{ $event->title }}"
                 style="width:100%;height:100%;object-fit:cover;display:block;">
            <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0.1) 0%,rgba(0,0,0,0.5) 100%);"></div>
        </div>
    @else
        @php
            $gradients = [
                'linear-gradient(135deg,#4f46e5 0%,#7c3aed 50%,#db2777 100%)',
                'linear-gradient(135deg,#059669 0%,#0d9488 50%,#0284c7 100%)',
                'linear-gradient(135deg,#ea580c 0%,#db2777 100%)',
                'linear-gradient(135deg,#0284c7 0%,#4f46e5 100%)',
                'linear-gradient(135deg,#7c3aed 0%,#4f46e5 100%)',
            ];
            $heroGrad = $gradients[$event->id % 5];
        @endphp
        <div style="width:100%;height:320px;background:{{ $heroGrad }};display:flex;align-items:center;justify-content:center;position:relative;">
            <span style="font-size:96px;font-weight:900;color:rgba(255,255,255,0.2);line-height:1;user-select:none;">{{ strtoupper(substr($event->title, 0, 1)) }}</span>
            <div style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 60%,rgba(0,0,0,0.15) 100%);"></div>
        </div>
    @endif

    {{-- Content --}}
    <div style="max-width:1040px;margin:0 auto;padding:40px 20px 60px;">

        <div style="display:grid;grid-template-columns:1fr 360px;gap:40px;align-items:start;">

            {{-- ── Left column: event details ───────────────────────────── --}}
            <div>

                {{-- Title & meta --}}
                <h1 style="font-size:28px;font-weight:900;color:#0f172a;margin:0 0 16px;line-height:1.2;font-family:'Instrument Sans',sans-serif;">{{ $event->title }}</h1>

                <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:28px;">
                    {{-- Date --}}
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:9px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 1px;">Data e Hora</p>
                            <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">
                                {{ $event->start_date->format('d \d\e M \d\e Y, H:i') }}
                                @if($event->end_date)
                                    <span style="color:#94a3b8;font-weight:400;"> até </span>{{ $event->end_date->format('d \d\e M \d\e Y, H:i') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Location --}}
                    @if($event->location || $event->city)
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:9px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p style="font-size:12px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 1px;">Local</p>
                                <p style="font-size:14px;font-weight:600;color:#1e293b;margin:0;">
                                    {{ $event->location }}{{ $event->location && $event->address ? ' — ' : '' }}{{ $event->address }}
                                </p>
                                @if($event->city || $event->state)
                                    <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $event->city }}{{ $event->city && $event->state ? ', ' : '' }}{{ $event->state }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Divider --}}
                <div style="border-top:1px solid #f1f5f9;margin-bottom:28px;"></div>

                {{-- Description --}}
                @if($event->description)
                    <div style="margin-bottom:32px;">
                        <h2 style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 12px;text-transform:uppercase;letter-spacing:0.5px;">Sobre o Evento</h2>
                        <div style="font-size:15px;color:#475569;line-height:1.75;">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>
                @endif

                {{-- Mobile: ticket types summary (shown below content on small screens) --}}
                <div style="display:none;" id="mobile-ticket-hint">
                    <div style="background:#f8fafc;border-radius:14px;border:1px solid #f1f5f9;padding:16px 20px;text-align:center;">
                        <p style="font-size:14px;color:#64748b;margin:0;">Role para baixo para selecionar seus ingressos →</p>
                    </div>
                </div>

            </div>

            {{-- ── Right column: ticket purchase ───────────────────────── --}}
            <div style="position:sticky;top:88px;">
                <form method="POST" action="{{ route('checkout.order') }}" x-data="ticketSelector()" id="ticket-form">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">

                    <div style="background:white;border-radius:20px;border:1px solid #f1f5f9;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">

                        {{-- Card header --}}
                        <div style="padding:20px 22px;border-bottom:1px solid #f1f5f9;">
                            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 3px;">Ingressos</p>
                            <p style="font-size:13px;color:#94a3b8;margin:0;">Selecione a quantidade desejada</p>
                        </div>

                        {{-- Ticket types --}}
                        <div style="padding:16px 22px;display:flex;flex-direction:column;gap:12px;">
                            @foreach($event->ticketTypes as $ticketType)
                                @php
                                    $onSale   = $ticketType->isOnSale();
                                    $soldOut  = $ticketType->available <= 0;
                                    $upcoming = $ticketType->sale_start->isFuture();
                                    $ended    = $ticketType->sale_end->isPast();
                                    $maxQty   = min($ticketType->available, $ticketType->max_per_user ?? 10);
                                    $isFree   = $ticketType->price == 0;
                                @endphp

                                <div style="border:1px solid {{ $onSale ? '#e2e8f0' : '#f1f5f9' }};border-radius:12px;padding:14px 16px;opacity:{{ $onSale ? '1' : '0.65' }};">
                                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                                        <div style="flex:1;min-width:0;">
                                            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 3px;">{{ $ticketType->name }}</p>
                                            @if($ticketType->description)
                                                <p style="font-size:12.5px;color:#94a3b8;margin:0 0 6px;line-height:1.4;">{{ $ticketType->description }}</p>
                                            @endif
                                            <p style="font-size:16px;font-weight:800;color:{{ $isFree ? '#16a34a' : '#4f46e5' }};margin:0;">
                                                {{ $isFree ? 'Gratuito' : 'R$ ' . number_format($ticketType->price, 2, ',', '.') }}
                                            </p>
                                        </div>

                                        <div style="flex-shrink:0;padding-top:2px;">
                                            @if($soldOut)
                                                <span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:100px;font-size:12px;font-weight:600;background:#fee2e2;color:#dc2626;">Esgotado</span>
                                            @elseif($upcoming)
                                                <span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:100px;font-size:12px;font-weight:600;background:#fef9c3;color:#ca8a04;">A partir de {{ $ticketType->sale_start->format('d/m') }}</span>
                                            @elseif($ended)
                                                <span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:100px;font-size:12px;font-weight:600;background:#f1f5f9;color:#94a3b8;">Encerrado</span>
                                            @else
                                                <div style="display:flex;align-items:center;gap:0;border:1px solid #e2e8f0;border-radius:9px;overflow:hidden;">
                                                    <button type="button"
                                                            onclick="adjustQty(this, -1)"
                                                            data-target="qty-{{ $ticketType->id }}"
                                                            style="width:32px;height:32px;background:#f8fafc;border:none;cursor:pointer;font-size:16px;color:#64748b;display:flex;align-items:center;justify-content:center;font-weight:600;">−</button>
                                                    <input type="number"
                                                           id="qty-{{ $ticketType->id }}"
                                                           name="items[{{ $ticketType->id }}][quantity]"
                                                           value="0"
                                                           min="0"
                                                           max="{{ $maxQty }}"
                                                           data-price="{{ $ticketType->price }}"
                                                           oninput="syncTotal()"
                                                           style="width:36px;height:32px;border:none;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;text-align:center;font-size:14px;font-weight:700;color:#0f172a;background:white;outline:none;-moz-appearance:textfield;"
                                                           readonly/>
                                                    <button type="button"
                                                            onclick="adjustQty(this, 1)"
                                                            data-target="qty-{{ $ticketType->id }}"
                                                            data-max="{{ $maxQty }}"
                                                            style="width:32px;height:32px;background:#f8fafc;border:none;cursor:pointer;font-size:16px;color:#64748b;display:flex;align-items:center;justify-content:center;font-weight:600;">+</button>
                                                </div>
                                                <input type="hidden" name="items[{{ $ticketType->id }}][ticket_type_id]" value="{{ $ticketType->id }}">
                                            @endif
                                        </div>
                                    </div>

                                    @if($onSale && !$soldOut)
                                        <p style="font-size:11.5px;color:#94a3b8;margin:8px 0 0;">{{ $ticketType->available }} disponível{{ $ticketType->available !== 1 ? 'is' : '' }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Total + CTA --}}
                        <div style="padding:16px 22px 20px;border-top:1px solid #f1f5f9;">
                            <div id="total-row" style="display:none;justify-content:space-between;align-items:center;margin-bottom:14px;">
                                <span style="font-size:13.5px;font-weight:600;color:#64748b;">Total</span>
                                <span id="total-display" style="font-size:18px;font-weight:800;color:#0f172a;">R$ 0,00</span>
                            </div>
                            @auth
                                <button type="submit" id="buy-btn" disabled
                                        style="width:100%;padding:13px;border-radius:12px;border:none;font-size:15px;font-weight:700;cursor:pointer;transition:opacity 0.15s;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;opacity:0.5;font-family:'Instrument Sans',sans-serif;">
                                    Selecione os ingressos
                                </button>
                            @else
                                <button type="button" id="buy-btn" disabled
                                        onclick="handleGuestBuy()"
                                        style="width:100%;padding:13px;border-radius:12px;border:none;font-size:15px;font-weight:700;cursor:pointer;transition:opacity 0.15s;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;opacity:0.5;font-family:'Instrument Sans',sans-serif;">
                                    Selecione os ingressos
                                </button>
                            @endauth
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    function adjustQty(btn, delta) {
        var targetId = btn.dataset.target;
        var input = document.getElementById(targetId);
        var max = parseInt(btn.dataset.max || input.max) || 99;
        var val = parseInt(input.value) || 0;
        val = Math.max(0, Math.min(max, val + delta));
        input.value = val;
        syncTotal();
    }

    function syncTotal() {
        var inputs = document.querySelectorAll('#ticket-form input[data-price]');
        var sum = 0;
        var hasAny = false;
        inputs.forEach(function (inp) {
            var qty   = parseInt(inp.value) || 0;
            var price = parseFloat(inp.dataset.price) || 0;
            sum += qty * price;
            if (qty > 0) hasAny = true;
        });

        var totalRow = document.getElementById('total-row');
        var totalDisplay = document.getElementById('total-display');
        var buyBtn = document.getElementById('buy-btn');

        if (hasAny) {
            totalRow.style.display = 'flex';
            totalDisplay.textContent = 'R$ ' + sum.toFixed(2).replace('.', ',');
            buyBtn.disabled = false;
            buyBtn.style.opacity = '1';
            buyBtn.textContent = sum > 0 ? 'Comprar Ingressos' : 'Confirmar Inscrição';
        } else {
            totalRow.style.display = 'none';
            buyBtn.disabled = true;
            buyBtn.style.opacity = '0.5';
            buyBtn.textContent = 'Selecione os ingressos';
        }
    }

    function handleGuestBuy() {
        // Redirect guest to login, storing this event page as the return URL
        var backUrl = encodeURIComponent(window.location.href);
        window.location.href = '/comprar/entrar?back=' + backUrl;
    }
    </script>
    @endpush
</x-layouts.app>
