<x-layouts.app :title="'Pedido Confirmado — TakeTicket'">
    <div class="max-w-3xl mx-auto px-4 py-12">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pagamento Confirmado!</h1>
            <p class="text-gray-500">Seu pedido foi realizado com sucesso. Você receberá os ingressos por e-mail em breve.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $order->event->title }}</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ $order->event->start_date->format('d M Y, H:i') }}</p>
                    <p class="text-gray-500 text-sm">{{ $order->event->location }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Pedido Nº</p>
                    <p class="font-mono font-semibold text-gray-800">{{ $order->id }}</p>
                </div>
            </div>

            <div class="border-t pt-4">
                <h3 class="font-medium text-gray-900 mb-3">Ingressos</h3>
                <div class="space-y-3">
                    @foreach($tickets as $ticket)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-sm">{{ $ticket->participant?->name ?? 'Participante' }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->participant?->email }}</p>
                                <p class="text-xs text-gray-400 font-mono mt-1">{{ $ticket->ticket_code }}</p>
                            </div>
                            @php
                                $statusLabel = match($ticket->status->value) {
                                    'valid' => 'Válido',
                                    'used' => 'Utilizado',
                                    'cancelled' => 'Cancelado',
                                    'processing' => 'Processando',
                                    default => ucfirst($ticket->status->value),
                                };
                            @endphp
                            <x-badge type="{{ $ticket->status->value }}">{{ $statusLabel }}</x-badge>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t pt-4 mt-4 flex justify-between font-semibold">
                <span>Total Pago</span>
                <span class="text-indigo-600">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/my-tickets') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium text-center transition">
                Ver Meus Ingressos
            </a>
            <a href="{{ route('home') }}"
               class="bg-white text-gray-700 border px-6 py-3 rounded-lg hover:bg-gray-50 font-medium text-center transition">
                Explorar Mais Eventos
            </a>
        </div>
    </div>
</x-layouts.app>
