<x-layouts.app title="TakeTicket - Find Events">
    {{-- Hero --}}
    <div class="bg-indigo-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Find Your Next Event</h1>
            <p class="text-xl text-indigo-100 mb-8">Discover and buy tickets for the best events near you</p>
            <form method="GET" action="{{ url('/') }}" class="max-w-2xl mx-auto flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search events..."
                       class="flex-1 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300">
                <input type="text" name="city" value="{{ request('city') }}"
                       placeholder="City"
                       class="w-40 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-36 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300"
                       placeholder="From">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-36 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300"
                       placeholder="To">
                <button type="submit" class="bg-indigo-800 px-6 py-3 rounded-lg hover:bg-indigo-900 font-medium">
                    Search
                </button>
            </form>
        </div>
    </div>

    {{-- Events Grid --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        @if($events->isEmpty())
            <p class="text-center text-gray-500 text-lg">No events found.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <a href="{{ route('event.show', $event->slug) }}" class="block bg-white rounded-lg shadow-sm border hover:shadow-md transition">
                        @if($event->banner)
                            <img src="{{ $event->banner }}" alt="{{ $event->title }}" class="w-full h-48 object-cover rounded-t-lg">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-t-lg flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">{{ substr($event->title, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-900">{{ $event->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $event->start_date->format('d M Y, H:i') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $event->city }}{{ $event->state ? ', ' . $event->state : '' }}</p>
                            @php
                                $minPrice = $event->ticketTypes->min('price');
                            @endphp
                            @if($minPrice !== null)
                                <p class="mt-2 font-semibold text-indigo-600">
                                    {{ $minPrice > 0 ? 'A partir de R$ ' . number_format($minPrice, 2, ',', '.') : 'Free' }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $events->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
