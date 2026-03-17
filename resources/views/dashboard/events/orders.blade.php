<x-layouts.dashboard header="Pedidos">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Pedidos — {{ $event->title }}</h2>
        <a href="{{ route('dashboard.events') }}" class="text-sm text-indigo-600 hover:underline">&larr; Voltar para Eventos</a>
    </div>

    <x-card>
        @if($orders->isEmpty())
            <p class="text-gray-500">Nenhum pedido ainda para este evento.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Pedido</th>
                            <th class="pb-3 font-medium">Comprador</th>
                            <th class="pb-3 font-medium">Qtd</th>
                            <th class="pb-3 font-medium">Total</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Pagamento</th>
                            <th class="pb-3 font-medium">Data</th>
                            <th class="pb-3 font-medium">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @php
                            $statusLabels = ['pending' => 'Pendente', 'awaiting_payment' => 'Aguardando', 'paid' => 'Pago', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado', 'expired' => 'Expirado'];
                        @endphp
                        @foreach($orders as $order)
                            <tr>
                                <td class="py-3 font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="py-3 text-gray-600">{{ $order->user?->name ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $order->items->sum('quantity') }}</td>
                                <td class="py-3 text-gray-600">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                <td class="py-3">
                                    <x-badge :type="$order->status->value">{{ $statusLabels[$order->status->value] ?? $order->status->value }}</x-badge>
                                </td>
                                <td class="py-3 text-gray-600">{{ $order->payment?->billing_type->value ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('dashboard.orders.show', [$event, $order]) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.dashboard>
