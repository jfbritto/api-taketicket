<x-layouts.app :title="'Pagamento Não Concluído — TakeTicket'">
    <div class="max-w-xl mx-auto px-4 py-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Pagamento Não Concluído</h1>
        <p class="text-gray-500 mb-8">
            Seu pagamento não foi concluído.
            @if($order)
                {{ $order->isExpired() ? 'Sua sessão expirou — por favor, inicie novamente.' : 'Você pode tentar novamente abaixo.' }}
            @else
                Navegue pelos nossos eventos e tente novamente.
            @endif
        </p>

        @if($order && !$order->isExpired())
            <div class="bg-white rounded-lg shadow-sm border p-4 mb-6 text-left">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Evento:</span> {{ $order->event->title }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="font-medium">Total:</span> R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('checkout.payment', $order) }}"
                   class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium text-center transition">
                    Tentar Novamente
                </a>
                <a href="{{ route('home') }}"
                   class="bg-white text-gray-700 border px-6 py-3 rounded-lg hover:bg-gray-50 font-medium text-center transition">
                    Explorar Eventos
                </a>
            </div>
        @else
            <a href="{{ route('home') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium inline-block transition">
                Explorar Eventos
            </a>
        @endif
    </div>
</x-layouts.app>
