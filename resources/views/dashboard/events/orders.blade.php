<x-layouts.dashboard header="Pedidos">
    <div class="space-y-5">

        {{-- Header card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 min-w-0">
                    <a href="{{ route('dashboard.events.show', $event) }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Pedidos</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $event->title }}</p>
                    </div>
                </div>
                <span class="text-sm text-gray-500">{{ $orders->total() }} pedido{{ $orders->total() !== 1 ? 's' : '' }}</span>
            </div>
        </div>

        {{-- Orders table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if($orders->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 font-medium">Nenhum pedido ainda</p>
                    <p class="text-sm text-gray-400 mt-1">Os pedidos aparecerão aqui conforme forem realizados.</p>
                </div>
            @else
                @php
                    $statusConfig = [
                        'pending'          => ['label' => 'Pendente',    'bg' => '#f3f4f6', 'color' => '#6b7280'],
                        'awaiting_payment' => ['label' => 'Aguardando',  'bg' => '#fef9c3', 'color' => '#a16207'],
                        'paid'             => ['label' => 'Pago',        'bg' => '#dcfce7', 'color' => '#16a34a'],
                        'cancelled'        => ['label' => 'Cancelado',   'bg' => '#fee2e2', 'color' => '#dc2626'],
                        'refunded'         => ['label' => 'Reembolsado', 'bg' => '#e0e7ff', 'color' => '#4338ca'],
                        'expired'          => ['label' => 'Expirado',    'bg' => '#f3f4f6', 'color' => '#9ca3af'],
                    ];
                @endphp
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 font-medium">Pedido</th>
                                <th class="px-6 py-3 font-medium">Comprador</th>
                                <th class="px-6 py-3 font-medium text-center">Qtd</th>
                                <th class="px-6 py-3 font-medium">Total</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium">Pagamento</th>
                                <th class="px-6 py-3 font-medium">Data</th>
                                <th class="px-6 py-3 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($orders as $order)
                                @php $sc = $statusConfig[$order->status->value] ?? ['label' => $order->status->value, 'bg' => '#f3f4f6', 'color' => '#6b7280']; @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-gray-900">#{{ $order->id }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-bold text-indigo-600">
                                                    {{ strtoupper(substr($order->user?->name ?? 'A', 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="text-gray-700 font-medium">{{ $order->user?->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-600">{{ $order->items->sum('quantity') }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                            {{ $sc['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $order->payment?->billing_type->value ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('dashboard.orders.show', [$event, $order]) }}"
                                           class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold transition">
                                            Ver detalhes →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</x-layouts.dashboard>
