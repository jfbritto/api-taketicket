<x-layouts.dashboard header="Participantes">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Participantes</h2>
            <p class="text-gray-500 mt-1">Todos os participantes de todos os seus eventos.</p>
        </div>
        <a href="{{ route('dashboard.participantes.export', array_merge(['event_id' => request('event_id')], request()->only('search'))) }}"
           class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2.5 rounded-xl hover:bg-green-700 text-sm font-semibold transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exportar CSV
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('dashboard.participantes') }}" class="mb-6 flex items-center gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Buscar por nome, e-mail ou CPF..."
               class="rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border text-sm w-72"/>
        <select name="event_id" class="rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border text-sm bg-white">
            <option value="">Todos os eventos</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                    {{ $event->title }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 text-sm font-semibold transition">
            Buscar
        </button>
        @if(request('search') || request('event_id'))
            <a href="{{ route('dashboard.participantes') }}" class="text-sm text-gray-500 hover:underline">Limpar</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($participants->isEmpty())
            <div class="text-center py-16">
                <div class="w-14 h-14 bg-violet-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-gray-700 font-semibold">Nenhum participante encontrado</p>
                <p class="text-gray-400 text-sm mt-1">Os participantes aparecerão aqui após a compra de ingressos.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 font-medium">Nome</th>
                            <th class="px-6 py-3 font-medium">E-mail</th>
                            <th class="px-6 py-3 font-medium">Documento</th>
                            <th class="px-6 py-3 font-medium">Evento</th>
                            <th class="px-6 py-3 font-medium">Tipo de Ingresso</th>
                            <th class="px-6 py-3 font-medium">Check-in</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($participants as $participant)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $participant->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $participant->email }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $participant->document ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600 max-w-36 truncate">{{ $participant->ticket?->event?->title ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $participant->ticket?->ticketType?->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($participant->ticket?->checked_in_at)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $participant->ticket->checked_in_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $participants->withQueryString()->links() }}
            </div>
        @endif
    </div>

</x-layouts.dashboard>
