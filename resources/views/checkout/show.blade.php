<x-layouts.checkout :title="'Checkout — ' . $order->event->title">
    <div class="max-w-5xl mx-auto px-4 py-8" x-data
        @order-expired.window="window.location.href = '/event/{{ $order->event->slug }}'">

        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Participant Information</h1>
            <x-countdown :expires-at="$order->expires_at->toIso8601String()" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Participant Forms --}}
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('checkout.participants', $order) }}">
                    @csrf

                    @php $index = 0; @endphp
                    @foreach($order->items as $item)
                        @foreach($tickets->where('order_item_id', $item->id) as $ticket)
                            <div class="bg-white rounded-lg shadow-sm border p-6 mb-4">
                                <h3 class="font-semibold text-gray-800 mb-4">
                                    Participant {{ ++$index }} — {{ $item->ticketType->name }}
                                </h3>

                                <input type="hidden" name="participants[{{ $index - 1 }}][ticket_id]" value="{{ $ticket->id }}">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                        <input type="text"
                                               name="participants[{{ $index - 1 }}][name]"
                                               value="{{ old("participants.{$loop->index}.name", $ticket->participant?->name !== 'Pending' ? $ticket->participant?->name : '') }}"
                                               required
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                        <input type="email"
                                               name="participants[{{ $index - 1 }}][email]"
                                               value="{{ old("participants.{$loop->index}.email", $ticket->participant?->email !== 'pending@pending.com' ? $ticket->participant?->email : '') }}"
                                               required
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="tel"
                                               name="participants[{{ $index - 1 }}][phone]"
                                               value="{{ old("participants.{$loop->index}.phone", $ticket->participant?->phone) }}"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Document (CPF/ID)</label>
                                        <input type="text"
                                               name="participants[{{ $index - 1 }}][document]"
                                               value="{{ old("participants.{$loop->index}.document", $ticket->participant?->document) }}"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                {{-- Custom Fields --}}
                                @if($order->event->customFields->count())
                                    <div class="mt-4 space-y-3">
                                        @foreach($order->event->customFields as $field)
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    {{ $field->label }}{{ $field->required ? ' *' : '' }}
                                                </label>
                                                <input type="text"
                                                       name="participants[{{ $index - 1 }}][custom_fields][{{ $field->id }}]"
                                                       {{ $field->required ? 'required' : '' }}
                                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endforeach

                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 font-semibold text-lg transition">
                        Continue to Payment
                    </button>
                </form>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border p-6 sticky top-4">
                    <h2 class="font-semibold text-gray-900 text-lg mb-4">Order Summary</h2>

                    <div class="text-sm text-gray-600 mb-4">
                        <p class="font-medium text-gray-800">{{ $order->event->title }}</p>
                        <p class="mt-1">{{ $order->event->start_date->format('d M Y, H:i') }}</p>
                        <p>{{ $order->event->location }}</p>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="py-2 flex justify-between text-sm">
                                <span class="text-gray-700">{{ $item->ticketType->name }} × {{ $item->quantity }}</span>
                                <span class="font-medium">R$ {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-3 mt-3 flex justify-between font-semibold">
                        <span>Total</span>
                        <span class="text-indigo-600">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.checkout>
