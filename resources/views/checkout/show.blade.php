<x-layouts.checkout :title="'Finalizar Compra — ' . $order->event->title">
<div style="max-width:1100px;margin:0 auto;padding:32px 20px 60px;" x-data
    @order-expired.window="window.location.href = '/event/{{ $order->event->slug }}'">

    {{-- Page header --}}
    <div style="margin-bottom:28px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px;letter-spacing:-0.4px;">Informações dos Participantes</h1>
                <p style="font-size:14px;color:#64748b;margin:0;">Preencha os dados de cada participante para gerar os ingressos.</p>
            </div>
            {{-- Countdown --}}
            <div style="display:flex;align-items:center;gap:8px;background:#fef9c3;border:1px solid #fde68a;border-radius:10px;padding:8px 14px;">
                <svg width="14" height="14" fill="none" stroke="#ca8a04" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span style="font-size:13px;font-weight:600;color:#92400e;">Reserva expira em</span>
                <x-countdown :expires-at="$order->expires_at->toIso8601String()" />
            </div>
        </div>

        {{-- Progress steps --}}
        <div style="display:flex;align-items:center;gap:0;margin-top:20px;">
            <div style="display:flex;align-items:center;gap:6px;">
                <div style="width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;">
                    <svg width="10" height="10" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span style="font-size:12px;font-weight:600;color:#4f46e5;">Ingressos</span>
            </div>
            <div style="width:40px;height:1px;background:#e2e8f0;margin:0 6px;"></div>
            <div style="display:flex;align-items:center;gap:6px;">
                <div style="width:20px;height:20px;border-radius:50%;background:#4f46e5;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:10px;font-weight:700;color:white;">2</span>
                </div>
                <span style="font-size:12px;font-weight:600;color:#4f46e5;">Participantes</span>
            </div>
            <div style="width:40px;height:1px;background:#e2e8f0;margin:0 6px;"></div>
            <div style="display:flex;align-items:center;gap:6px;">
                <div style="width:20px;height:20px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;">
                    <span style="font-size:10px;font-weight:700;color:#94a3b8;">3</span>
                </div>
                <span style="font-size:12px;font-weight:500;color:#94a3b8;">Pagamento</span>
            </div>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <svg width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p style="font-size:13px;color:#dc2626;margin:0;font-weight:500;">{{ $errors->first() }}</p>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

        {{-- ── Left: Participant Forms ── --}}
        <div>
            <form method="POST" action="{{ route('checkout.participants', $order) }}">
                @csrf

                @php $index = 0; @endphp
                @foreach($order->items as $item)
                    @foreach($tickets->where('order_item_id', $item->id) as $ticket)
                        @php $index++; @endphp
                        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:24px;margin-bottom:16px;box-shadow:0 2px 12px rgba(0,0,0,0.04);">

                            {{-- Card header --}}
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f8fafc;">
                                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="16" height="16" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Participante {{ $index }}</p>
                                    <p style="font-size:12px;color:#7c3aed;margin:0;font-weight:600;">{{ $item->ticketType->name }}</p>
                                </div>
                                <div style="margin-left:auto;">
                                    <span style="font-size:12px;color:#94a3b8;font-weight:500;">* campos obrigatórios</span>
                                </div>
                            </div>

                            <input type="hidden" name="participants[{{ $index - 1 }}][ticket_id]" value="{{ $ticket->id }}">

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                {{-- Name --}}
                                <div>
                                    <label style="display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                                        Nome Completo *
                                        <x-tooltip text="Informe o nome completo do participante conforme documento de identidade. Este nome será exibido no ingresso." />
                                    </label>
                                    <input type="text"
                                           name="participants[{{ $index - 1 }}][name]"
                                           value="{{ old("participants.{$loop->index}.name", $ticket->participant?->name !== 'Pending' ? $ticket->participant?->name : '') }}"
                                           required
                                           placeholder="João da Silva"
                                           style="width:100%;box-sizing:border-box;padding:10px 13px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:#f9fafb;outline:none;font-family:inherit;"
                                           onfocus="this.style.borderColor='#4f46e5';this.style.background='white'" onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label style="display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                                        E-mail *
                                        <x-tooltip text="O ingresso será enviado para este e-mail após a confirmação do pagamento." />
                                    </label>
                                    <input type="email"
                                           name="participants[{{ $index - 1 }}][email]"
                                           value="{{ old("participants.{$loop->index}.email", $ticket->participant?->email !== 'pending@pending.com' ? $ticket->participant?->email : '') }}"
                                           required
                                           placeholder="email@exemplo.com"
                                           style="width:100%;box-sizing:border-box;padding:10px 13px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:#f9fafb;outline:none;font-family:inherit;"
                                           onfocus="this.style.borderColor='#4f46e5';this.style.background='white'" onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                                </div>

                                {{-- Phone --}}
                                <div>
                                    <label style="display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                                        Telefone / WhatsApp
                                        <x-tooltip text="Número de contato para comunicações sobre o evento." />
                                    </label>
                                    <input type="tel"
                                           name="participants[{{ $index - 1 }}][phone]"
                                           value="{{ old("participants.{$loop->index}.phone", $ticket->participant?->phone) }}"
                                           placeholder="(11) 99999-9999"
                                           class="mask-phone"
                                           style="width:100%;box-sizing:border-box;padding:10px 13px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:#f9fafb;outline:none;font-family:inherit;"
                                           onfocus="this.style.borderColor='#4f46e5';this.style.background='white'" onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                                </div>

                                {{-- CPF --}}
                                <div>
                                    <label style="display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                                        CPF
                                        <x-tooltip text="Pode ser solicitado na entrada do evento para verificação de identidade." />
                                    </label>
                                    <input type="text"
                                           name="participants[{{ $index - 1 }}][document]"
                                           value="{{ old("participants.{$loop->index}.document", $ticket->participant?->document) }}"
                                           placeholder="000.000.000-00"
                                           class="mask-cpf"
                                           style="width:100%;box-sizing:border-box;padding:10px 13px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:#f9fafb;outline:none;font-family:inherit;"
                                           onfocus="this.style.borderColor='#4f46e5';this.style.background='white'" onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                                </div>
                            </div>

                            {{-- Custom Fields --}}
                            @if($order->event->customFields->count())
                                <div style="margin-top:18px;padding-top:18px;border-top:1px solid #f1f5f9;display:flex;flex-direction:column;gap:14px;">
                                    <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin:0;">Informações adicionais do evento</p>
                                    @foreach($order->event->customFields as $field)
                                        <div>
                                            <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                                                {{ $field->label }}{{ $field->required ? ' *' : '' }}
                                            </label>
                                            <input type="text"
                                                   name="participants[{{ $index - 1 }}][custom_fields][{{ $field->id }}]"
                                                   {{ $field->required ? 'required' : '' }}
                                                   style="width:100%;box-sizing:border-box;padding:10px 13px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:#f9fafb;outline:none;font-family:inherit;"
                                                   onfocus="this.style.borderColor='#4f46e5';this.style.background='white'" onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endforeach

                <button type="submit"
                        style="width:100%;padding:14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 20px rgba(79,70,229,0.4);font-family:inherit;letter-spacing:-0.2px;"
                        onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    Continuar para Pagamento →
                </button>
            </form>
        </div>

        {{-- ── Right: Order Summary ── --}}
        <div style="position:sticky;top:80px;">
            <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.04);">

                {{-- Event info header --}}
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
                <div style="background:{{ $grad }};padding:18px 20px;display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="font-size:18px;font-weight:900;color:rgba(255,255,255,0.9);">{{ strtoupper(substr($order->event->title, 0, 1)) }}</span>
                    </div>
                    <div style="min-width:0;">
                        <p style="font-size:14px;font-weight:700;color:white;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $order->event->title }}</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.75);margin:2px 0 0;">{{ $order->event->start_date->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                </div>

                {{-- Location --}}
                @if($order->event->location || $order->event->city)
                    <div style="padding:12px 20px;border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:8px;">
                        <svg width="13" height="13" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span style="font-size:12.5px;color:#64748b;">{{ $order->event->location }}{{ $order->event->city ? ' · ' . $order->event->city : '' }}</span>
                    </div>
                @endif

                {{-- Items --}}
                <div style="padding:16px 20px;">
                    <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 12px;">Resumo do Pedido</p>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($order->items as $item)
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                                <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                                    <div style="width:24px;height:24px;border-radius:6px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <svg width="12" height="12" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    </div>
                                    <span style="font-size:13px;color:#374151;font-weight:500;">{{ $item->ticketType->name }} <span style="color:#94a3b8;">× {{ $item->quantity }}</span></span>
                                </div>
                                <span style="font-size:13px;font-weight:700;color:#0f172a;flex-shrink:0;">
                                    {{ $item->unit_price == 0 ? 'Grátis' : 'R$ ' . number_format($item->unit_price * $item->quantity, 2, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Total --}}
                <div style="padding:14px 20px 18px;border-top:1px solid #f1f5f9;">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:14px;font-weight:700;color:#0f172a;">Total</span>
                        <span style="font-size:18px;font-weight:800;background:linear-gradient(135deg,#4f46e5,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
                            {{ $order->total_amount == 0 ? 'Grátis' : 'R$ ' . number_format($order->total_amount, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Trust --}}
                <div style="padding:12px 20px;background:#f8fafc;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:center;gap:6px;">
                    <svg width="12" height="12" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span style="font-size:12px;color:#64748b;font-weight:500;">Compra protegida e segura</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    function applyPhoneMask(el) {
        el.addEventListener('input', function () {
            var v = this.value.replace(/\D/g, '').slice(0, 11);
            if (v.length <= 10) {
                v = v.replace(/^(\d{2})(\d{0,4})(\d{0,4})$/, function(_, a, b, c) {
                    return b ? '(' + a + ') ' + b + (c ? '-' + c : '') : a ? '(' + a : a;
                });
            } else {
                v = v.replace(/^(\d{2})(\d{5})(\d{0,4})$/, function(_, a, b, c) {
                    return '(' + a + ') ' + b + (c ? '-' + c : '');
                });
            }
            this.value = v;
        });
    }

    function applyCpfMask(el) {
        el.addEventListener('input', function () {
            var v = this.value.replace(/\D/g, '').slice(0, 11);
            v = v.replace(/^(\d{3})(\d)/, '$1.$2');
            v = v.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            v = v.replace(/\.(\d{3})(\d)/, '.$1-$2');
            this.value = v;
        });
    }

    document.querySelectorAll('.mask-phone').forEach(applyPhoneMask);
    document.querySelectorAll('.mask-cpf').forEach(applyCpfMask);

    // Apply to existing values on load
    document.querySelectorAll('.mask-phone, .mask-cpf').forEach(function(el) {
        el.dispatchEvent(new Event('input'));
    });
});
</script>
@endpush
</x-layouts.checkout>
