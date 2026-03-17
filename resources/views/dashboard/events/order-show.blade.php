<x-layouts.dashboard header="Detalhe do Pedido">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Pedido #{{ $order->id }} — {{ $event->title }}</h2>
        <a href="{{ route('dashboard.orders', $event) }}" class="text-sm text-indigo-600 hover:underline">&larr; Voltar para Pedidos</a>
    </div>

    @php
        $statusLabels = ['pending' => 'Pendente', 'awaiting_payment' => 'Aguardando', 'paid' => 'Pago', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado', 'expired' => 'Expirado'];
        $ticketStatusLabels = ['valid' => 'Válido', 'used' => 'Utilizado', 'cancelled' => 'Cancelado'];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <x-card>
            <h3 class="font-semibold text-gray-700 mb-4">Informações do Pedido</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Status</dt>
                    <dd><x-badge :type="$order->status->value">{{ $statusLabels[$order->status->value] ?? $order->status->value }}</x-badge></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Total</dt>
                    <dd class="font-medium">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Taxa da Plataforma</dt>
                    <dd>R$ {{ number_format($order->platform_fee, 2, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Valor ao Organizador</dt>
                    <dd>R$ {{ number_format($order->organizer_amount, 2, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Data</dt>
                    <dd>{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <h3 class="font-semibold text-gray-700 mb-4">Comprador e Pagamento</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Comprador</dt>
                    <dd>{{ $order->user?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">E-mail</dt>
                    <dd>{{ $order->user?->email ?? '—' }}</dd>
                </div>
                @if($order->payment)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Forma de Pagamento</dt>
                        <dd>{{ $order->payment->billing_type->value ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status do Pagamento</dt>
                        <dd><x-badge :type="$order->payment->status->value">{{ ucfirst($order->payment->status->value) }}</x-badge></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Pago em</dt>
                        <dd>{{ $order->payment->paid_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                @endif
            </dl>
        </x-card>
    </div>

    <x-card class="mb-6">
        <h3 class="font-semibold text-gray-700 mb-4">Itens do Pedido</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 border-b">
                    <tr>
                        <th class="pb-3 font-medium">Tipo de Ingresso</th>
                        <th class="pb-3 font-medium">Quantidade</th>
                        <th class="pb-3 font-medium">Valor Unitário</th>
                        <th class="pb-3 font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="py-3 text-gray-700">{{ $item->ticketType?->name ?? '—' }}</td>
                            <td class="py-3 text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-3 text-gray-600">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                            <td class="py-3 text-gray-600">R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    @if($tickets->isNotEmpty())
        <x-card>
            <h3 class="font-semibold text-gray-700 mb-4">Ingressos</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Código</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Participante</th>
                            <th class="pb-3 font-medium">Check-in</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="py-3 font-mono text-gray-700">{{ $ticket->ticket_code }}</td>
                                <td class="py-3">
                                    <x-badge :type="$ticket->status->value">{{ $ticketStatusLabels[$ticket->status->value] ?? $ticket->status->value }}</x-badge>
                                </td>
                                <td class="py-3 text-gray-600">{{ $ticket->participant?->name ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $ticket->checked_in_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</x-layouts.dashboard>
