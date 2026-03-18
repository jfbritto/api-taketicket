<x-layouts.dashboard header="Ingressos">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Ingressos — {{ $event->title }}</h2>
        <a href="{{ route('dashboard.events') }}" class="text-sm text-indigo-600 hover:underline">&larr; Voltar para Eventos</a>
    </div>

    <form id="tk-search-form" method="GET" action="{{ route('dashboard.tickets', $event) }}" class="mb-6 flex items-center gap-3">
        <input
            id="tk-search-input"
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Buscar por código do ingresso..."
            autocomplete="off"
            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border text-sm w-72"
        />
        @if(request('search'))
            <a href="{{ route('dashboard.tickets', $event) }}" class="text-sm text-gray-500 hover:underline">Limpar</a>
        @endif
    </form>

    <div id="tk-results-container" style="transition:opacity 0.15s;">
    <x-card>
        @if($tickets->isEmpty())
            <p class="text-gray-500">Nenhum ingresso encontrado.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Código</th>
                            <th class="pb-3 font-medium">Participante</th>
                            <th class="pb-3 font-medium">Tipo</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Check-in</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @php
                            $ticketStatusLabels = ['valid' => 'Válido', 'used' => 'Utilizado', 'cancelled' => 'Cancelado'];
                        @endphp
                        @foreach($tickets as $ticket)
                            <tr>
                                <td class="py-3 font-mono text-gray-700">{{ $ticket->ticket_code }}</td>
                                <td class="py-3 text-gray-600">{{ $ticket->participant?->name ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $ticket->ticketType?->name ?? '—' }}</td>
                                <td class="py-3">
                                    <x-badge :type="$ticket->status->value">{{ $ticketStatusLabels[$ticket->status->value] ?? $ticket->status->value }}</x-badge>
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
    </div>

    @push('scripts')
    <script>
    (function () {
        var form      = document.getElementById('tk-search-form');
        var input     = document.getElementById('tk-search-input');
        var container = document.getElementById('tk-results-container');
        var timer;

        function doSearch() {
            var params = new URLSearchParams(new FormData(form));
            container.style.opacity = '0.5';
            fetch(form.action + '?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var fresh = doc.getElementById('tk-results-container');
                if (fresh) container.innerHTML = fresh.innerHTML;
                container.style.opacity = '1';
                history.replaceState({}, '', form.action + '?' + params.toString());
            })
            .catch(function () { container.style.opacity = '1'; });
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(doSearch, 400);
        });
    })();
    </script>
    @endpush
</x-layouts.dashboard>
