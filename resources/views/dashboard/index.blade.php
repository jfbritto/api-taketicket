<x-layouts.dashboard header="Dashboard">
    {{-- Welcome --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Olá, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-gray-500 mt-1">Aqui está um resumo dos seus eventos e vendas.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total de Eventos</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalEvents }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total em Vendas</p>
            <p class="text-3xl font-bold text-green-600">R$ {{ number_format($totalSales, 2, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total de Participantes</p>
            <p class="text-3xl font-bold text-gray-900">{{ $totalParticipants }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Taxa de Check-in</p>
            <p class="text-3xl font-bold text-amber-600">{{ $checkinRate }}%</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ url('/dashboard/events/create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 font-semibold transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Criar Evento
        </a>
        <a href="{{ url('/dashboard/events') }}"
           class="inline-flex items-center gap-2 bg-white text-gray-700 border border-gray-200 px-5 py-2.5 rounded-xl hover:bg-gray-50 font-semibold transition">
            Ver todos os eventos
        </a>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">Pedidos Recentes</h3>
        </div>

        @if($recentOrders->isEmpty())
            <div class="text-center py-12">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-gray-500">Nenhum pedido ainda.</p>
                <p class="text-gray-400 text-sm mt-1">Os pedidos aparecerão aqui quando você tiver vendas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 font-medium">Pedido</th>
                            <th class="px-6 py-3 font-medium">Comprador</th>
                            <th class="px-6 py-3 font-medium">Evento</th>
                            <th class="px-6 py-3 font-medium">Valor</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                            @php
                                $statusLabels = ['pending' => 'Pendente', 'awaiting_payment' => 'Aguardando', 'paid' => 'Pago', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado', 'expired' => 'Expirado'];
                            @endphp
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-medium text-gray-900">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $order->user->name }}</td>
                                <td class="px-6 py-4 text-gray-600 max-w-48 truncate">{{ $order->event->title }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4"><x-badge :type="$order->status->value">{{ $statusLabels[$order->status->value] ?? $order->status->value }}</x-badge></td>
                                <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.dashboard>
