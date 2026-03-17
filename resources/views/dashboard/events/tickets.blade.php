<x-layouts.dashboard header="Tickets">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Tickets — {{ $event->title }}</h2>
        <a href="{{ route('dashboard.events') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Events</a>
    </div>

    <form method="GET" action="{{ route('dashboard.tickets', $event) }}" class="mb-6 flex items-center gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by ticket code..."
            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border text-sm w-72"
        />
        <button type="submit"
                class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('dashboard.tickets', $event) }}" class="text-sm text-gray-500 hover:underline">Clear</a>
        @endif
    </form>

    <x-card>
        @if($tickets->isEmpty())
            <p class="text-gray-500">No tickets found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Code</th>
                            <th class="pb-3 font-medium">Participant</th>
                            <th class="pb-3 font-medium">Type</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Check-in</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="py-3 font-mono text-gray-700">{{ $ticket->ticket_code }}</td>
                                <td class="py-3 text-gray-600">{{ $ticket->participant?->name ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $ticket->ticketType?->name ?? '—' }}</td>
                                <td class="py-3">
                                    <x-badge :type="$ticket->status->value">{{ ucfirst($ticket->status->value) }}</x-badge>
                                </td>
                                <td class="py-3 text-gray-600">{{ $ticket->checked_in_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $tickets->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.dashboard>
