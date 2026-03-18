<x-layouts.app :title="'Pedido Confirmado — TakeTicket'">

@php
    $gradients = [
        'linear-gradient(135deg,#4f46e5,#7c3aed)',
        'linear-gradient(135deg,#059669,#0d9488)',
        'linear-gradient(135deg,#ea580c,#db2777)',
        'linear-gradient(135deg,#0284c7,#4f46e5)',
        'linear-gradient(135deg,#7c3aed,#4f46e5)',
    ];
    $grad = $gradients[$order->event->id % 5];
@endphp

<style>
    @keyframes pop-in {
        0% { transform: scale(0.5); opacity: 0; }
        70% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes fade-up {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-pop  { animation: pop-in  0.5s cubic-bezier(.34,1.56,.64,1) both; }
    .anim-fade { animation: fade-up 0.5s ease both; }
    .anim-1 { animation-delay: 0.15s; }
    .anim-2 { animation-delay: 0.25s; }
    .anim-3 { animation-delay: 0.38s; }
</style>

<div style="max-width:680px;margin:0 auto;padding:48px 20px 80px;">

    {{-- Success icon --}}
    <div style="text-align:center;margin-bottom:32px;">
        <div class="anim-pop" style="display:inline-flex;align-items:center;justify-content:center;width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 8px 32px rgba(79,70,229,0.35);margin-bottom:20px;">
            <svg width="32" height="32" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <div class="anim-fade anim-1">
            <h1 style="font-size:28px;font-weight:900;color:#0f172a;margin:0 0 8px;letter-spacing:-0.6px;font-family:'Instrument Sans',sans-serif;">
                @if($order->total_amount == 0) Inscrição Confirmada! @else Pagamento Confirmado! @endif
            </h1>
            <p style="font-size:15px;color:#64748b;margin:0;line-height:1.6;">
                @if($order->total_amount == 0)
                    Sua inscrição foi realizada com sucesso. Você receberá os ingressos por e-mail em breve.
                @else
                    Seu pagamento foi aprovado. Os ingressos serão enviados para o e-mail de cada participante.
                @endif
            </p>
        </div>
    </div>

    {{-- Main card --}}
    <div class="anim-fade anim-2" style="background:white;border-radius:20px;border:1px solid #f1f5f9;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);margin-bottom:20px;">

        {{-- Event header --}}
        <div style="background:{{ $grad }};padding:20px 24px;display:flex;align-items:center;gap:14px;">
            <div style="width:46px;height:46px;border-radius:12px;background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="font-size:22px;font-weight:900;color:white;">{{ strtoupper(substr($order->event->title, 0, 1)) }}</span>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:16px;font-weight:800;color:white;margin:0 0 3px;letter-spacing:-0.3px;">{{ $order->event->title }}</p>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:5px;">
                        <svg width="12" height="12" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span style="font-size:12px;color:rgba(255,255,255,0.8);">{{ $order->event->start_date->format('d \d\e M \d\e Y, H:i') }}</span>
                    </div>
                    @if($order->event->location)
                        <div style="display:flex;align-items:center;gap:5px;">
                            <svg width="12" height="12" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span style="font-size:12px;color:rgba(255,255,255,0.8);">{{ $order->event->location }}{{ $order->event->city ? ' · ' . $order->event->city : '' }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <p style="font-size:10px;font-weight:600;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.5px;margin:0 0 2px;">Pedido</p>
                <p style="font-size:18px;font-weight:800;color:white;margin:0;font-family:monospace;letter-spacing:-0.5px;">#{{ $order->id }}</p>
            </div>
        </div>

        {{-- Tickets --}}
        <div style="padding:20px 24px;">
            <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 14px;">Ingressos</p>

            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($tickets as $ticket)
                    @php
                        $isValid = $ticket->status->value === 'valid';
                        $isProcessing = $ticket->status->value === 'processing';
                        $statusLabel = match($ticket->status->value) {
                            'valid'      => 'Válido',
                            'used'       => 'Utilizado',
                            'cancelled'  => 'Cancelado',
                            'processing' => 'Processando',
                            default      => ucfirst($ticket->status->value),
                        };
                        $statusBg = match($ticket->status->value) {
                            'valid'      => '#f0fdf4',
                            'processing' => '#fef9c3',
                            'cancelled'  => '#fef2f2',
                            default      => '#f1f5f9',
                        };
                        $statusColor = match($ticket->status->value) {
                            'valid'      => '#16a34a',
                            'processing' => '#92400e',
                            'cancelled'  => '#dc2626',
                            default      => '#64748b',
                        };
                        $statusDot = match($ticket->status->value) {
                            'valid'      => '#22c55e',
                            'processing' => '#f59e0b',
                            'cancelled'  => '#ef4444',
                            default      => '#94a3b8',
                        };
                        $initials = strtoupper(substr($ticket->participant?->name ?? 'P', 0, 1));
                        $colors = ['#4f46e5','#7c3aed','#0284c7','#059669','#db2777'];
                        $bg = $colors[$ticket->id % 5];
                    @endphp
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;background:#f8fafc;border-radius:12px;border:1px solid #f1f5f9;">
                        {{-- Avatar --}}
                        <div style="width:38px;height:38px;border-radius:50%;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-size:15px;font-weight:800;color:white;">{{ $initials }}</span>
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 2px;">{{ $ticket->participant?->name ?? 'Participante' }}</p>
                            <p style="font-size:12px;color:#64748b;margin:0 0 4px;">{{ $ticket->participant?->email }}</p>
                            <p style="font-size:11px;color:#94a3b8;margin:0;font-family:monospace;letter-spacing:0.3px;">{{ $ticket->ticket_code }}</p>
                        </div>

                        {{-- Status --}}
                        <div style="display:flex;align-items:center;gap:5px;background:{{ $statusBg }};border-radius:100px;padding:5px 10px;flex-shrink:0;">
                            <div style="width:6px;height:6px;border-radius:50%;background:{{ $statusDot }};"></div>
                            <span style="font-size:12px;font-weight:600;color:{{ $statusColor }};">{{ $statusLabel }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Total --}}
        <div style="padding:16px 24px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:14px;font-weight:700;color:#0f172a;">Total Pago</span>
            <span style="font-size:20px;font-weight:800;background:linear-gradient(135deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                {{ $order->total_amount == 0 ? 'Grátis' : 'R$ ' . number_format($order->total_amount, 2, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Email notice --}}
    <div class="anim-fade anim-2" style="display:flex;align-items:center;gap:10px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:14px 18px;margin-bottom:24px;">
        <svg width="18" height="18" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        <p style="font-size:13px;color:#1d4ed8;margin:0;font-weight:500;">
            Os ingressos com QR Code serão enviados para o e-mail de cada participante. Verifique também a caixa de spam.
        </p>
    </div>

    {{-- CTA buttons --}}
    <div class="anim-fade anim-3" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ url('/my-tickets') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;box-shadow:0 4px 20px rgba(79,70,229,0.4);letter-spacing:-0.2px;"
           onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            Ver Meus Ingressos
        </a>
        <a href="{{ route('home') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:white;color:#374151;border:1.5px solid #e2e8f0;border-radius:12px;font-size:15px;font-weight:700;text-decoration:none;letter-spacing:-0.2px;"
           onmouseover="this.style.borderColor='#4f46e5';this.style.color='#4f46e5'" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151'">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Explorar Mais Eventos
        </a>
    </div>

</div>
</x-layouts.app>
