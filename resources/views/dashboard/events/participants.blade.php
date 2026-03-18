<x-layouts.dashboard header="Participantes">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Participantes — {{ $event->title }}</h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.participants.export', array_merge(['event' => $event->id], request()->only('search'))) }}"
               class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                Exportar CSV
            </a>
            <a href="{{ route('dashboard.events') }}" class="text-sm text-indigo-600 hover:underline">&larr; Voltar para Eventos</a>
        </div>
    </div>

    <form id="ep-search-form" method="GET" action="{{ route('dashboard.participants', $event) }}" class="mb-6 flex items-center gap-3">
        <input
            id="ep-search-input"
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Buscar por nome, e-mail ou documento..."
            autocomplete="off"
            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border text-sm w-80"
        />
        @if(request('search'))
            <a href="{{ route('dashboard.participants', $event) }}" class="text-sm text-gray-500 hover:underline">Limpar</a>
        @endif
    </form>

    <div id="ep-results-container" style="transition:opacity 0.15s;">
    <x-card>
        @if($participants->isEmpty())
            <p class="text-gray-500">Nenhum participante encontrado.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="pb-3 font-medium">Nome</th>
                            <th class="pb-3 font-medium">E-mail</th>
                            <th class="pb-3 font-medium">Telefone</th>
                            <th class="pb-3 font-medium">CPF</th>
                            <th class="pb-3 font-medium">Tipo de Ingresso</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Check-in</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @php
                            $ticketStatusLabels = ['valid' => 'Válido', 'used' => 'Utilizado', 'cancelled' => 'Cancelado'];
                        @endphp
                        @foreach($participants as $participant)
                            <tr>
                                <td class="py-3 font-medium text-gray-900">{{ $participant->name }}</td>
                                <td class="py-3 text-gray-600">{{ $participant->email }}</td>
                                <td class="py-3 text-gray-600">{{ $participant->phone ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $participant->document ?? '—' }}</td>
                                <td class="py-3 text-gray-600">{{ $participant->ticket?->ticketType?->name ?? '—' }}</td>
                                <td class="py-3">
                                    @if($participant->ticket)
                                        <x-badge :type="$participant->ticket->status->value">{{ $ticketStatusLabels[$participant->ticket->status->value] ?? $participant->ticket->status->value }}</x-badge>
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
    </div>

    @push('scripts')
    <script>
    (function () {
        var form      = document.getElementById('ep-search-form');
        var input     = document.getElementById('ep-search-input');
        var container = document.getElementById('ep-results-container');
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
                var fresh = doc.getElementById('ep-results-container');
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
