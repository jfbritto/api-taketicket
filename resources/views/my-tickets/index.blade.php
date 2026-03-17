<x-layouts.app title="My Tickets">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">My Tickets</h1>

        @if($tickets->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                <p class="text-gray-500 text-lg">You have no tickets yet.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold transition">
                    Browse Events
                </a>
            </div>
        @else
            @foreach($tickets as $eventId => $eventTickets)
                @php $event = $eventTickets->first()->event; @endphp
                <div class="mb-8">
                    <div class="mb-3">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $event->title }}</h2>
                        <p class="text-sm text-gray-500">{{ $event->start_date->format('d M Y, H:i') }} &mdash; {{ $event->location }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($eventTickets as $ticket)
                            <div class="bg-white rounded-lg shadow-sm border p-5 flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-800">{{ $ticket->ticketType->name }}</span>
                                    @if($ticket->status === \App\Enums\TicketStatus::VALID)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Valid</span>
                                    @elseif($ticket->status === \App\Enums\TicketStatus::USED)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Used</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
                                    @endif
                                </div>

                                <div class="text-sm text-gray-600">
                                    <p><span class="font-medium">Participant:</span> {{ $ticket->participant?->name ?? 'N/A' }}</p>
                                    <p class="mt-1"><span class="font-medium">Ticket Code:</span> {{ $ticket->ticket_code }}</p>
                                </div>

                                <a href="{{ route('my-tickets.show', $ticket) }}"
                                   class="mt-auto inline-block text-center bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 text-sm font-semibold transition">
                                    View Ticket
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</x-layouts.app>
