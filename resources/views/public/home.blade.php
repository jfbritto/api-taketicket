<x-layouts.app title="TakeTicket - Encontre Eventos">
    {{-- Hero --}}
    <div class="relative bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-600 text-white overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative max-w-4xl mx-auto px-4 py-20 text-center">
            <span class="inline-block bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4 backdrop-blur-sm">
                🎉 Plataforma de ingressos online
            </span>
            <h1 class="text-5xl font-bold mb-4 leading-tight">Encontre Seu<br>Próximo Evento</h1>
            <p class="text-xl text-indigo-100 mb-10">Descubra e compre ingressos para os melhores eventos perto de você</p>

            <div class="bg-white rounded-2xl p-2 shadow-2xl max-w-3xl mx-auto">
                <form method="GET" action="{{ url('/') }}">
                    <div class="flex flex-wrap gap-2">
                        <div class="flex-1 min-w-48 relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Buscar eventos..."
                                   class="w-full pl-9 pr-3 py-3 text-gray-900 rounded-xl border-0 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-300 text-sm">
                        </div>
                        <div class="w-36 relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <input type="text" name="city" value="{{ request('city') }}"
                                   placeholder="Cidade"
                                   class="w-full pl-9 pr-3 py-3 text-gray-900 rounded-xl border-0 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-300 text-sm">
                        </div>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               title="Data de início"
                               class="w-36 px-3 py-3 text-gray-900 rounded-xl border-0 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-300 text-sm">
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               title="Data de término"
                               class="w-36 px-3 py-3 text-gray-900 rounded-xl border-0 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-300 text-sm">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 font-semibold transition text-sm whitespace-nowrap">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Events Grid --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        @if(request('search') || request('city') || request('date_from') || request('date_to'))
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-600">
                    @if($events->total() > 0)
                        <span class="font-semibold text-gray-900">{{ $events->total() }}</span> evento(s) encontrado(s)
                    @else
                        Nenhum evento encontrado para os filtros aplicados
                    @endif
                </p>
                <a href="{{ url('/') }}" class="text-sm text-indigo-600 hover:underline">Limpar filtros</a>
            </div>
        @else
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Eventos em destaque</h2>
        @endif

        @if($events->isEmpty())
            <div class="text-center py-20">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-lg mb-2">Nenhum evento encontrado.</p>
                <p class="text-gray-400 text-sm">Tente ajustar os filtros de busca.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <a href="{{ route('event.show', $event->slug) }}"
                       class="group block bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 overflow-hidden">

                        {{-- Event image / placeholder --}}
                        <div class="relative">
                            @if($event->banner)
                                <img src="{{ $event->banner }}" alt="{{ $event->title }}"
                                     class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                @php
                                    $colors = [
                                        ['from-indigo-500', 'to-violet-600'],
                                        ['from-rose-500', 'to-pink-600'],
                                        ['from-amber-500', 'to-orange-600'],
                                        ['from-emerald-500', 'to-teal-600'],
                                        ['from-sky-500', 'to-blue-600'],
                                        ['from-purple-500', 'to-fuchsia-600'],
                                    ];
                                    $color = $colors[$event->id % count($colors)];
                                @endphp
                                <div class="w-full h-52 bg-gradient-to-br {{ $color[0] }} {{ $color[1] }} flex flex-col items-center justify-center">
                                    <span class="text-5xl font-bold text-white/30 mb-1">{{ substr($event->title, 0, 1) }}</span>
                                    <span class="text-white/60 text-xs font-medium uppercase tracking-widest">Evento</span>
                                </div>
                            @endif

                            {{-- Price badge on image --}}
                            @php $minPrice = $event->ticketTypes->min('price'); @endphp
                            @if($minPrice !== null)
                                <div class="absolute bottom-3 right-3">
                                    <span class="bg-white/95 backdrop-blur-sm text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">
                                        {{ $minPrice > 0 ? 'A partir de R$ ' . number_format($minPrice, 2, ',', '.') : 'Gratuito' }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 text-lg leading-snug mb-3 group-hover:text-indigo-600 transition-colors line-clamp-2">
                                {{ $event->title }}
                            </h3>

                            <div class="space-y-1.5 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $event->start_date->format('d \d\e M \d\e Y') }} às {{ $event->start_date->format('H:i') }}</span>
                                </div>
                                @if($event->city)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $event->city }}{{ $event->state ? ', ' . $event->state : '' }}</span>
                                    </div>
                                @endif
                                @if($event->location)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="truncate">{{ $event->location }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $events->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
