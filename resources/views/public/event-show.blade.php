<x-layouts.app :title="$event->title">
    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Banner --}}
        @if($event->banner)
            <img src="{{ $event->banner }}" alt="{{ $event->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
        @else
            <div class="w-full h-64 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg mb-6 flex items-center justify-center">
                <span class="text-white text-6xl font-bold">{{ substr($event->title, 0, 1) }}</span>
            </div>
        @endif

        {{-- Event Info --}}
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>
        <div class="flex flex-wrap gap-4 text-gray-600 mb-6">
            <span>{{ $event->start_date->format('d M Y, H:i') }}{{ $event->end_date ? ' - ' . $event->end_date->format('d M Y, H:i') : '' }}</span>
            <span>{{ $event->location }}{{ $event->address ? ', ' . $event->address : '' }}</span>
            <span>{{ $event->city }}{{ $event->state ? ', ' . $event->state : '' }}</span>
        </div>

        @if($event->description)
            <div class="prose max-w-none mb-8">
                {!! nl2br(e($event->description)) !!}
            </div>
        @endif

        {{-- Ticket Types --}}
        <x-card title="Tickets">
            <form method="POST" action="{{ route('checkout.order') }}" x-data="ticketSelector()" id="ticket-form">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="space-y-4">
                    @foreach($event->ticketTypes as $ticketType)
                        @php
                            $onSale = $ticketType->isOnSale();
                            $soldOut = $ticketType->available <= 0;
                            $upcoming = $ticketType->sale_start->isFuture();
                            $ended = $ticketType->sale_end->isPast();
                            $maxQty = min($ticketType->available, $ticketType->max_per_user ?? 10);
                        @endphp
                        <div class="flex items-center justify-between p-4 border rounded-lg {{ $onSale ? '' : 'opacity-60' }}">
                            <div>
                                <h4 class="font-semibold">{{ $ticketType->name }}</h4>
                                @if($ticketType->description)
                                    <p class="text-sm text-gray-500">{{ $ticketType->description }}</p>
                                @endif
                                <p class="text-lg font-bold text-indigo-600 mt-1">
                                    {{ $ticketType->price > 0 ? 'R$ ' . number_format($ticketType->price, 2, ',', '.') : 'Free' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($soldOut)
                                    <x-badge type="cancelled">Sold Out</x-badge>
                                @elseif($upcoming)
                                    <x-badge type="pending">Starts {{ $ticketType->sale_start->format('d/m') }}</x-badge>
                                @elseif($ended)
                                    <x-badge type="expired">Ended</x-badge>
                                @else
                                    <select name="items[{{ $ticketType->id }}][quantity]"
                                            @change="updateTotal()"
                                            data-price="{{ $ticketType->price }}"
                                            class="rounded-lg border-gray-300 w-20 text-center">
                                        @for($i = 0; $i <= $maxQty; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <input type="hidden" name="items[{{ $ticketType->id }}][ticket_type_id]" value="{{ $ticketType->id }}">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <div class="text-lg font-semibold" x-show="total > 0">
                        Total: R$ <span x-text="total.toFixed(2).replace('.', ',')"></span>
                    </div>
                    <button type="submit" :disabled="total === 0 && !hasFreeTickets"
                            class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        Buy Tickets
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
    function ticketSelector() {
        return {
            total: 0,
            hasFreeTickets: false,
            updateTotal() {
                let sum = 0;
                let free = false;
                document.querySelectorAll('#ticket-form select[data-price]').forEach(sel => {
                    const qty = parseInt(sel.value) || 0;
                    const price = parseFloat(sel.dataset.price) || 0;
                    sum += qty * price;
                    if (qty > 0 && price === 0) free = true;
                });
                this.total = sum;
                this.hasFreeTickets = free;
            }
        };
    }
    </script>
    @endpush
</x-layouts.app>
