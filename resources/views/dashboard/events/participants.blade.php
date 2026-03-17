<x-layouts.dashboard header="Participants">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Participants — {{ $event->title }}</h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.participants.export', array_merge(['event' => $event->id], request()->only('search'))) }}"
               class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                Export CSV
            </a>
            <a href="{{ route('dashboard.events') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Events</a>
        </div>
    </div>

    <form method="GET" action="{{ route('dashboard.participants', $event) }}" class="mb-6 flex items-center gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by name, email or document..."
            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border text-sm w-72"
        />
        <button type="submit"
                class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('dashboard.participants', $event) }}" class="text-sm text-gray-500 hover:underline">Clear</a>
        @endif
    </form>

    <x-card>
        @if($participants->isEmpty())
            <p class="text-gray-500">No participants found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Name</th>
                            <th class="pb-3 font-medium">Email</th>
                            <th class="pb-3 font-medium">Phone</th>
                            <th class="pb-3 font-medium">Document</th>
                            <th class="pb-3 font-medium">Ticket Type</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Check-in</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($participants as $participant)
                            <tr>
                                <td class="py-3 font-medium text-gray-900">{{ $participant->name }}</td>
                                <td class="py-3 text-gray-600">{{ $participant->email }}</td>
                                <td class="py-3 text-gray-600">{{ $participant->phone ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $participant->document ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $participant->ticket?->ticketType?->name ?? '—' }}</td>
                                <td class="py-3">
                                    @if($participant->ticket)
                                        <x-badge :type="$participant->ticket->status->value">{{ ucfirst($participant->ticket->status->value) }}</x-badge>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 text-gray-600">{{ $participant->ticket?->checked_in_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $participants->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.dashboard>
