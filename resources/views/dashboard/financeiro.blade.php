<x-layouts.dashboard header="Financeiro">

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Financeiro</h2>
        <p class="text-gray-500 mt-1">Acompanhe seus recebimentos e o histórico de pedidos pagos.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Valor Líquido</p>
            <p class="text-3xl font-bold text-green-600">R$ {{ number_format($totalNet, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Após taxas da plataforma</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Total Bruto</p>
            <p class="text-3xl font-bold text-gray-800">R$ {{ number_format($totalGross, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Valor total arrecadado</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Taxa da Plataforma</p>
            <p class="text-3xl font-bold text-red-500">R$ {{ number_format($totalFee, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Total retido pela TakeTicket</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Pedidos Pagos</p>
            <p class="text-3xl font-bold text-gray-800">{{ $totalPaidCount }}</p>
            <p class="text-xs text-gray-400 mt-1">Total de transações confirmadas</p>
        </div>
    </div>

    {{-- Recent Paid Orders --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Histórico de Recebimentos</h3>
            <p class="text-sm text-gray-500 mt-0.5">Últimos 15 pedidos pagos</p>
        </div>

        @if($recentOrders->isEmpty())
            <div class="text-center py-16">
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-gray-700 font-semibold">Nenhum pagamento recebido ainda</p>
                <p class="text-gray-400 text-sm mt-1">Os recebimentos aparecerão aqui quando houver vendas confirmadas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 font-medium">Pedido</th>
                            <th class="px-6 py-3 font-medium">Comprador</th>
                            <th class="px-6 py-3 font-medium">Evento</th>
                            <th class="px-6 py-3 font-medium">Bruto</th>
                            <th class="px-6 py-3 font-medium">Taxa</th>
                            <th class="px-6 py-3 font-medium">Líquido</th>
                            <th class="px-6 py-3 font-medium">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-800">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $order->user->name }}</td>
                                <td class="px-6 py-4 text-gray-600 max-w-40 truncate">{{ $order->event->title }}</td>
                                <td class="px-6 py-4 text-gray-700">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-red-500">- R$ {{ number_format($order->platform_fee, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 font-semibold text-green-600">R$ {{ number_format($order->organizer_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layouts.dashboard>
