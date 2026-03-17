<x-layouts.app title="TakeTicket - Encontre Eventos">
    {{-- Hero --}}
    <div class="bg-indigo-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Encontre Seu Próximo Evento</h1>
            <p class="text-xl text-indigo-100 mb-8">Descubra e compre ingressos para os melhores eventos perto de você</p>
            <form method="GET" action="{{ url('/') }}" class="max-w-3xl mx-auto">
                <div class="flex flex-wrap gap-2 justify-center">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Buscar eventos..."
                           class="flex-1 min-w-48 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300">
                    <input type="text" name="city" value="{{ request('city') }}"
                           placeholder="Cidade"
                           class="w-40 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           title="Data de início"
                           class="w-36 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           title="Data de término"
                           class="w-36 rounded-lg px-4 py-3 text-gray-900 border-0 focus:ring-2 focus:ring-indigo-300">
                    <button type="submit" class="bg-indigo-800 px-6 py-3 rounded-lg hover:bg-indigo-900 font-medium">
                        Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Events Grid --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        @if($events->isEmpty())
            <div class="text-center py-16">
                <p class="text-gray-500 text-lg mb-4">Nenhum evento encontrado.</p>
                <p class="text-gray-400 text-sm">Tente ajustar os filtros de busca.</p>
            </div>
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
                                {{ $event->start_date->format('d \d\e M \d\e Y, H:i') }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $event->city }}{{ $event->state ? ', ' . $event->state : '' }}</p>
                            @php
                                $minPrice = $event->ticketTypes->min('price');
                            @endphp
                            @if($minPrice !== null)
                                <p class="mt-2 font-semibold text-indigo-600">
                                    {{ $minPrice > 0 ? 'A partir de R$ ' . number_format($minPrice, 2, ',', '.') : 'Gratuito' }}
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
