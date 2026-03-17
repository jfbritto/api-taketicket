<x-mail::message>
# Seus ingressos estão prontos!

Olá {{ $order->user->name }},

Seus ingressos para **{{ $order->event->title }}** foram confirmados.

**Detalhes do pedido:**
- Pedido #{{ $order->id }}
- Total: R$ {{ number_format($order->total_amount, 2, ',', '.') }}

@foreach($order->items as $item)
@foreach($item->tickets as $ticket)
**Ingresso:** {{ $ticket->ticket_code }}
**Participante:** {{ $ticket->participant->name ?? 'N/A' }}
**Tipo:** {{ $item->ticketType->name }}

---
@endforeach
@endforeach

Apresente o código do ingresso ou o QR Code no evento.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
