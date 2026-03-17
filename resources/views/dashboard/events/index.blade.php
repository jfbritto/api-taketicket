<x-layouts.dashboard header="Eventos">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Meus Eventos</h2>
            <p class="text-gray-500 mt-1">Gerencie e acompanhe todos os seus eventos.</p>
        </div>
        <a href="{{ route('dashboard.events.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 font-semibold transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Criar Evento
        </a>
    </div>

    {{-- Status filter --}}
    <form method="GET" action="{{ route('dashboard.events') }}" class="mb-6 flex items-center gap-3">
        <label for="status" class="text-sm font-medium text-gray-600">Filtrar por status:</label>
        <select name="status" id="status" onchange="this.form.submit()"
                class="rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border text-sm bg-white">
            <option value="">Todos</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
        </select>
    </form>

    {{-- Events Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        @if($events->isEmpty())
            <div class="text-center py-16">
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-gray-700 font-semibold mb-1">Nenhum evento ainda</p>
                <p class="text-gray-400 text-sm mb-5">Crie seu primeiro evento e comece a vender ingressos.</p>
                <a href="{{ route('dashboard.events.create') }}"
                   class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 font-semibold transition shadow-sm text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Criar meu primeiro evento
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 font-medium">Evento</th>
                            <th class="px-6 py-3 font-medium">Data</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Ingressos Vendidos</th>
                            <th class="px-6 py-3 font-medium">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php
                            $statusLabels = ['draft' => 'Rascunho', 'published' => 'Publicado', 'cancelled' => 'Cancelado'];
                        @endphp
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $gradients = [
                                                0 => 'from-indigo-500 to-purple-600',
                                                1 => 'from-emerald-500 to-teal-600',
                                                2 => 'from-orange-500 to-pink-600',
                                                3 => 'from-sky-500 to-indigo-600',
                                                4 => 'from-rose-500 to-orange-600',
                                                5 => 'from-violet-500 to-purple-600',
                                            ];
                                            $grad = $gradients[$event->id % 6];
                                        @endphp
                                        <div class="w-10 h-10 bg-gradient-to-br {{ $grad }} rounded-xl flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $event->title }}</p>
                                            <p class="text-xs text-gray-400">{{ $event->city ?? '' }}{{ $event->city && $event->state ? ', ' : '' }}{{ $event->state ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $event->start_date->format('d/m/Y H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :type="$event->status->value">{{ $statusLabels[$event->status->value] ?? $event->status->value }}</x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-gray-900">{{ $event->orders_count ?? 0 }}</span>
                                        <span class="text-xs text-gray-400">ingresso(s)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('dashboard.events.show', $event) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                        Gerenciar →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $events->withQueryString()->links() }}
            </div>
        @endif
    </div>

</x-layouts.dashboard>
