<x-layouts.dashboard header="Orders">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Orders — {{ $event->title }}</h2>
        <a href="{{ route('dashboard.events') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Events</a>
    </div>

    <x-card>
        @if($orders->isEmpty())
            <p class="text-gray-500">No orders yet for this event.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Order #</th>
                            <th class="pb-3 font-medium">Buyer</th>
                            <th class="pb-3 font-medium">Qty</th>
                            <th class="pb-3 font-medium">Total</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Payment</th>
                            <th class="pb-3 font-medium">Date</th>
                            <th class="pb-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($orders as $order)
                            <tr>
                                <td class="py-3 font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="py-3 text-gray-600">{{ $order->user?->name ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $order->items->sum('quantity') }}</td>
                                <td class="py-3 text-gray-600">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                <td class="py-3">
                                    <x-badge :type="$order->status->value">{{ ucfirst($order->status->value) }}</x-badge>
                                </td>
                                <td class="py-3 text-gray-600">{{ $order->payment?->billing_type->value ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('dashboard.orders.show', [$event, $order]) }}"
                                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">View</a>
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
