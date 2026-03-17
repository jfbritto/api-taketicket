<x-layouts.dashboard header="Eventos">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Meus Eventos</h2>
        <a href="{{ route('dashboard.events.create') }}"
           class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            + Criar Evento
        </a>
    </div>

    {{-- Status filter --}}
    <form method="GET" action="{{ route('dashboard.events') }}" class="mb-6 flex items-center gap-3">
        <label for="status" class="text-sm font-medium text-gray-700">Filtrar por status:</label>
        <select name="status" id="status" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border text-sm">
            <option value="">Todos</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
        </select>
    </form>

    <x-card>
        @if($events->isEmpty())
            <p class="text-gray-500">Nenhum evento ainda. <a href="{{ route('dashboard.events.create') }}" class="text-indigo-600 hover:underline">Crie seu primeiro evento.</a></p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Título</th>
                            <th class="pb-3 font-medium">Data</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Vendidos</th>
                            <th class="pb-3 font-medium">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($events as $event)
                            <tr>
                                <td class="py-3 font-medium text-gray-900">{{ $event->title }}</td>
                                <td class="py-3 text-gray-600">{{ $event->start_date->format('d/m/Y H:i') }}</td>
                                <td class="py-3">
                                    @php
                                        $statusLabels = ['draft' => 'Rascunho', 'published' => 'Publicado', 'cancelled' => 'Cancelado'];
                                    @endphp
                                    <x-badge :type="$event->status->value">{{ $statusLabels[$event->status->value] ?? $event->status->value }}</x-badge>
                                </td>
                                <td class="py-3 text-gray-600">{{ $event->orders_count ?? 0 }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dashboard.events.edit', $event) }}"
                                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Editar</a>

                                        @if($event->status->value === 'draft')
                                            <form method="POST" action="{{ route('dashboard.events.publish', $event) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-green-600 hover:text-green-800 text-xs font-medium">
                                                    Publicar
                                                </button>
                                            </form>
                                        @endif

                                        @if($event->status->value !== 'cancelled')
                                            <form method="POST" action="{{ route('dashboard.events.cancel', $event) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        onclick="return confirm('Tem certeza que deseja cancelar este evento?')"
                                                        class="text-red-600 hover:text-red-800 text-xs font-medium">
                                                    Cancelar
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
