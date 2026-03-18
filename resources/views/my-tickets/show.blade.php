<x-layouts.app title="Ingresso — {{ $ticket->ticket_code }}">
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var qr = qrcode(0, 'M');
            qr.addData('{{ $ticket->qr_code_payload }}');
            qr.make();
            document.getElementById('qr-code').innerHTML = qr.createImgTag(5, 8);
        });
    </script>
    @endpush

<div style="max-width:460px;margin:0 auto;padding:36px 20px;">

    {{-- Back --}}
    <a href="{{ route('my-tickets') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13.5px;font-weight:500;color:#64748b;text-decoration:none;margin-bottom:24px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Meus Ingressos
    </a>

    {{-- Ticket card --}}
    <div style="background:white;border-radius:20px;border:1px solid #f1f5f9;overflow:hidden;">

        {{-- Gradient header --}}
        @php
            $gradients = [
                'linear-gradient(135deg,#4f46e5,#7c3aed)',
                'linear-gradient(135deg,#059669,#0d9488)',
                'linear-gradient(135deg,#ea580c,#db2777)',
                'linear-gradient(135deg,#0284c7,#4f46e5)',
                'linear-gradient(135deg,#dc2626,#ea580c)',
                'linear-gradient(135deg,#7c3aed,#6d28d9)',
            ];
            $grad = $gradients[$ticket->event->id % 6];
            $statusMap = [
                'valid'     => ['label' => 'Válido',    'dot' => '#16a34a', 'bg' => '#dcfce7', 'color' => '#15803d'],
                'used'      => ['label' => 'Utilizado', 'dot' => '#94a3b8', 'bg' => '#f1f5f9', 'color' => '#64748b'],
                'cancelled' => ['label' => 'Cancelado', 'dot' => '#dc2626', 'bg' => '#fee2e2', 'color' => '#dc2626'],
            ];
            $s = $statusMap[$ticket->status->value] ?? $statusMap['valid'];
        @endphp
        <div style="background:{{ $grad }};padding:24px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div>
                    <p style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:0.5px;margin:0 0 5px;">Evento</p>
                    <h1 style="font-size:18px;font-weight:800;color:white;margin:0 0 8px;line-height:1.3;">{{ $ticket->event->title }}</h1>
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <span style="font-size:13px;color:rgba(255,255,255,0.75);display:flex;align-items:center;gap:5px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $ticket->event->start_date->format('d \d\e M \d\e Y, H:i') }}
                        </span>
                        @if($ticket->event->location)
                            <span style="font-size:13px;color:rgba(255,255,255,0.75);display:flex;align-items:center;gap:5px;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $ticket->event->location }}
                            </span>
                        @endif
                    </div>
                </div>
                <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:100px;font-size:12px;font-weight:700;background:rgba(255,255,255,0.2);color:white;white-space:nowrap;flex-shrink:0;backdrop-filter:blur(4px);">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $s['dot'] }};display:inline-block;"></span>
                    {{ $s['label'] }}
                </span>
            </div>
        </div>

        {{-- Details --}}
        <div style="padding:0 24px;">

            {{-- Ticket type + code --}}
            <div style="padding:18px 0;border-bottom:1px solid #f1f5f9;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <p style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 5px;">Tipo de Ingresso</p>
                    <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">{{ $ticket->ticketType->name }}</p>
                </div>
                <div>
                    <p style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 5px;">Código</p>
                    <p style="font-size:13px;font-family:monospace;color:#475569;margin:0;word-break:break-all;">{{ $ticket->ticket_code }}</p>
                </div>
            </div>

            {{-- Participant --}}
            @if($ticket->participant)
                <div style="padding:18px 0;border-bottom:1px solid #f1f5f9;">
                    <p style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 12px;">Participante</p>
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                        @php
                            $pName = $ticket->participant->name;
                            $pInit = strtoupper(substr($pName, 0, 1)) . strtoupper(substr(strstr($pName, ' ') ?: ' x', 1, 1));
                        @endphp
                        <div style="width:40px;height:40px;border-radius:50%;background:{{ $grad }};display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:white;flex-shrink:0;">
                            {{ $pInit }}
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 2px;">{{ $pName }}</p>
                            <p style="font-size:12.5px;color:#64748b;margin:0;">{{ $ticket->participant->email }}</p>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @if($ticket->participant->phone)
                            <div style="display:flex;justify-content:space-between;font-size:13px;">
                                <span style="color:#94a3b8;font-weight:500;">Telefone</span>
                                <span style="color:#334155;font-weight:600;">{{ $ticket->participant->phone }}</span>
                            </div>
                        @endif
                        @if($ticket->participant->document)
                            <div style="display:flex;justify-content:space-between;font-size:13px;">
                                <span style="color:#94a3b8;font-weight:500;">CPF</span>
                                <span style="color:#334155;font-weight:600;font-family:monospace;">{{ $ticket->participant->document }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- QR Code --}}
            <div style="padding:24px 0;text-align:center;">
                <p style="font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin:0 0 16px;">QR Code de Entrada</p>
                <div id="qr-code" style="display:flex;justify-content:center;margin-bottom:12px;"></div>
                <p style="font-size:11px;color:#cbd5e1;font-family:monospace;margin:0;">{{ $ticket->qr_code_payload }}</p>
            </div>

        </div>

        {{-- Footer note --}}
        <div style="background:#f8fafc;border-top:1px solid #f1f5f9;padding:14px 24px;text-align:center;">
            <p style="font-size:12.5px;color:#94a3b8;margin:0;display:flex;align-items:center;justify-content:center;gap:6px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Apresente este QR Code na entrada do evento
            </p>
        </div>

    </div>
</div>
</x-layouts.app>
