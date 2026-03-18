<x-layouts.app title="Meus Ingressos">
<div style="max-width:860px;margin:0 auto;padding:40px 20px;">

    {{-- Page Header --}}
    <div style="margin-bottom:32px;">
        <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 5px;font-family:'Instrument Sans',sans-serif;">Meus Ingressos</h1>
        <p style="font-size:14px;color:#64748b;margin:0;">Acesse e apresente seus ingressos nos eventos.</p>
    </div>

    @if($tickets->isEmpty())
        {{-- Empty state --}}
        <div style="background:white;border-radius:20px;border:1px solid #f1f5f9;padding:72px 24px;text-align:center;">
            <div style="width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,#ede9fe,#dbeafe);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="30" height="30" fill="none" stroke="#7c3aed" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <p style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 8px;">Você ainda não tem ingressos</p>
            <p style="font-size:14px;color:#94a3b8;margin:0 0 28px;">Explore os eventos disponíveis e garanta o seu lugar.</p>
            <a href="{{ route('home') }}"
               style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;padding:11px 24px;border-radius:12px;font-size:14px;font-weight:600;text-decoration:none;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                Explorar Eventos
            </a>
        </div>
    @else
        @php
            $gradients = [
                'linear-gradient(135deg,#4f46e5,#7c3aed)',
                'linear-gradient(135deg,#059669,#0d9488)',
                'linear-gradient(135deg,#ea580c,#db2777)',
                'linear-gradient(135deg,#0284c7,#4f46e5)',
                'linear-gradient(135deg,#dc2626,#ea580c)',
                'linear-gradient(135deg,#7c3aed,#6d28d9)',
            ];
        @endphp

        @foreach($tickets as $eventId => $eventTickets)
            @php
                $event = $eventTickets->first()->event;
                $grad  = $gradients[$event->id % 6];
            @endphp

            {{-- Event group --}}
            <div style="margin-bottom:28px;">

                {{-- Event header --}}
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px;">
                    <div style="width:44px;height:44px;border-radius:12px;background:{{ $grad }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:16px;font-weight:800;color:#0f172a;margin:0 0 3px;">{{ $event->title }}</p>
                        <p style="font-size:13px;color:#64748b;margin:0;display:flex;align-items:center;gap:10px;">
                            <span style="display:inline-flex;align-items:center;gap:4px;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $event->start_date->format('d \d\e M \d\e Y, H:i') }}
                            </span>
                            @if($event->location)
                                <span style="color:#cbd5e1;">·</span>
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $event->location }}
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Ticket cards grid --}}
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
                    @foreach($eventTickets as $ticket)
                        @php
                            $statusMap = [
                                'valid'     => ['label' => 'Válido',    'dot' => '#16a34a', 'bg' => '#dcfce7', 'color' => '#15803d'],
                                'used'      => ['label' => 'Utilizado', 'dot' => '#94a3b8', 'bg' => '#f1f5f9', 'color' => '#64748b'],
                                'cancelled' => ['label' => 'Cancelado', 'dot' => '#dc2626', 'bg' => '#fee2e2', 'color' => '#dc2626'],
                            ];
                            $s = $statusMap[$ticket->status->value] ?? $statusMap['valid'];
                        @endphp
                        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px;display:flex;flex-direction:column;gap:0;">

                            {{-- Top: type + status --}}
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                                <div>
                                    <p style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 4px;">Tipo de Ingresso</p>
                                    <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">{{ $ticket->ticketType->name }}</p>
                                </div>
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:100px;font-size:12px;font-weight:600;background:{{ $s['bg'] }};color:{{ $s['color'] }};white-space:nowrap;flex-shrink:0;">
                                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $s['dot'] }};display:inline-block;"></span>
                                    {{ $s['label'] }}
                                </span>
                            </div>

                            {{-- Participant --}}
                            @if($ticket->participant)
                                <div style="display:flex;align-items:center;gap:10px;padding:12px 0;border-top:1px solid #f8fafc;">
                                    @php
                                        $pName = $ticket->participant->name;
                                        $pInit = strtoupper(substr($pName, 0, 1)) . strtoupper(substr(strstr($pName, ' ') ?: ' x', 1, 1));
                                    @endphp
                                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $grad }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:white;flex-shrink:0;">
                                        {{ $pInit }}
                                    </div>
                                    <div>
                                        <p style="font-size:13px;font-weight:600;color:#334155;margin:0 0 1px;">{{ $pName }}</p>
                                        <p style="font-size:11.5px;color:#94a3b8;margin:0;">{{ $ticket->participant->email }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Code --}}
                            <div style="padding:10px 0;border-top:1px solid #f8fafc;margin-bottom:16px;">
                                <p style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 3px;">Código</p>
                                <p style="font-size:13px;font-family:monospace;color:#475569;margin:0;letter-spacing:0.5px;">{{ $ticket->ticket_code }}</p>
                            </div>

                            {{-- Action --}}
                            <a href="{{ route('my-tickets.show', $ticket) }}"
                               style="display:flex;align-items:center;justify-content:center;gap:7px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;padding:10px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;margin-top:auto;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                Ver Ingresso &amp; QR Code
                            </a>
                        </div>
                    @endforeach
                </div>

            </div>
        @endforeach
    @endif

</div>
</x-layouts.app>
