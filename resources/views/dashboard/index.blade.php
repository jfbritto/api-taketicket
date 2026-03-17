<x-layouts.dashboard header="Dashboard">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card>
            <p class="text-sm text-gray-500">Total de Eventos</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalEvents }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Total em Vendas</p>
            <p class="text-3xl font-bold text-green-600">R$ {{ number_format($totalSales, 2, ',', '.') }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Total de Participantes</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalParticipants }}</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Taxa de Check-in</p>
            <p class="text-3xl font-bold text-indigo-600">{{ $checkinRate }}%</p>
        </x-card>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-8">
        <a href="{{ url('/dashboard/events/create') }}" class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            + Criar Evento
        </a>
    </div>

    {{-- Recent Orders --}}
    <x-card title="Pedidos Recentes">
        @if($recentOrders->isEmpty())
            <p class="text-gray-500">Nenhum pedido ainda.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Pedido</th>
                            <th class="pb-3 font-medium">Comprador</th>
                            <th class="pb-3 font-medium">Evento</th>
                            <th class="pb-3 font-medium">Valor</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($recentOrders as $order)
                            @php
                                $statusLabels = ['pending' => 'Pendente', 'awaiting_payment' => 'Aguardando', 'paid' => 'Pago', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado', 'expired' => 'Expirado'];
                            @endphp
                            <tr>
                                <td class="py-3">#{{ $order->id }}</td>
                                <td class="py-3">{{ $order->user->name }}</td>
                                <td class="py-3">{{ $order->event->title }}</td>
                                <td class="py-3">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                <td class="py-3"><x-badge :type="$order->status->value">{{ $statusLabels[$order->status->value] ?? $order->status->value }}</x-badge></td>
                                <td class="py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-layouts.dashboard>
