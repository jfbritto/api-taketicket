<x-layouts.app :title="'Payment Cancelled — TakeTicket'">
    <div class="max-w-xl mx-auto px-4 py-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Not Completed</h1>
        <p class="text-gray-500 mb-8">
            Your payment was not completed.
            @if($order)
                {{ $order->isExpired() ? 'Your session has expired — please start again.' : 'You can try again below.' }}
            @else
                Please browse our events and try again.
            @endif
        </p>

        @if($order && !$order->isExpired())
            <div class="bg-white rounded-lg shadow-sm border p-4 mb-6 text-left">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Event:</span> {{ $order->event->title }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="font-medium">Total:</span> R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('checkout.payment', $order) }}"
                   class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium text-center transition">
                    Try Again
                </a>
                <a href="{{ route('home') }}"
                   class="bg-white text-gray-700 border px-6 py-3 rounded-lg hover:bg-gray-50 font-medium text-center transition">
                    Browse Events
                </a>
            </div>
        @else
            <a href="{{ route('home') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium inline-block transition">
                Browse Events
            </a>
        @endif
    </div>
</x-layouts.app>
