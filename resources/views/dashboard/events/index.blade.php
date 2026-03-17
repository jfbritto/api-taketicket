<x-layouts.dashboard header="Events">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">My Events</h2>
        <a href="{{ route('dashboard.events.create') }}"
           class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            + Create Event
        </a>
    </div>

    {{-- Status filter --}}
    <form method="GET" action="{{ route('dashboard.events') }}" class="mb-6 flex items-center gap-3">
        <label for="status" class="text-sm font-medium text-gray-700">Filter by status:</label>
        <select name="status" id="status" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border text-sm">
            <option value="">All</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </form>

    <x-card>
        @if($events->isEmpty())
            <p class="text-gray-500">No events yet. <a href="{{ route('dashboard.events.create') }}" class="text-indigo-600 hover:underline">Create your first event.</a></p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Title</th>
                            <th class="pb-3 font-medium">Date</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Sold</th>
                            <th class="pb-3 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($events as $event)
                            <tr>
                                <td class="py-3 font-medium text-gray-900">{{ $event->title }}</td>
                                <td class="py-3 text-gray-600">{{ $event->start_date->format('d/m/Y H:i') }}</td>
                                <td class="py-3">
                                    <x-badge :type="$event->status->value">{{ ucfirst($event->status->value) }}</x-badge>
                                </td>
                                <td class="py-3 text-gray-600">{{ $event->orders_count ?? 0 }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dashboard.events.edit', $event) }}"
                                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>

                                        @if($event->status->value === 'draft')
                                            <form method="POST" action="{{ route('dashboard.events.publish', $event) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-green-600 hover:text-green-800 text-xs font-medium">
                                                    Publish
                                                </button>
                                            </form>
                                        @endif

                                        @if($event->status->value !== 'cancelled')
                                            <form method="POST" action="{{ route('dashboard.events.cancel', $event) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        onclick="return confirm('Cancel this event?')"
                                                        class="text-red-600 hover:text-red-800 text-xs font-medium">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $events->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.dashboard>
