<x-layouts.dashboard header="Check-in">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div x-data="checkinApp()" x-init="init()">

        {{-- Header --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Check-in</h2>
            <p class="text-gray-500 mt-1">Valide ingressos manualmente ou por leitura de QR Code.</p>
        </div>

        {{-- Event Selector (opcional, apenas para ver estatísticas) --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <label for="event-select" class="block text-sm font-semibold text-gray-700 mb-1">Estatísticas por evento <span class="text-xs text-gray-400 font-normal">(opcional)</span></label>
            <p class="text-xs text-gray-400 mb-3">Selecione um evento para acompanhar os check-ins em tempo real. O check-in funciona sem seleção.</p>
            <select id="event-select" x-model="selectedEventId" @change="updateStats()"
                    class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 border text-sm bg-white">
                <option value="">-- Todos os eventos --</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}"
                            data-tickets="{{ $event->tickets_count }}"
                            data-checked="{{ $event->checked_in_count }}">
                        {{ $event->title }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Stats --}}
        <div x-show="selectedEventId" x-cloak class="grid grid-cols-3 gap-5 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="checkedIn"></p>
                    <p class="text-xs font-medium text-gray-500">Check-ins realizados</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900" x-text="totalTickets"></p>
                    <p class="text-xs font-medium text-gray-500">Total de ingressos</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600" x-text="totalTickets > 0 ? Math.round((checkedIn / totalTickets) * 100) + '%' : '0%'"></p>
                    <p class="text-xs font-medium text-gray-500">Taxa de presença</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Manual Check-in --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Check-in Manual</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-4">Digite o código do ingresso para validar.</p>
                    <form @submit.prevent="validateTicket()">
                        <div class="flex gap-2">
                            <input type="text" x-model="ticketCode"
                                   placeholder="Ex: TKT-XXXXXXXX"
                                   class="flex-1 rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5 border text-sm font-mono"
                                   :disabled="loading" />
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 disabled:opacity-50 text-sm font-semibold transition shadow-sm"
                                    :disabled="loading || !ticketCode">
                                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-show="!loading">Validar</span>
                                <span x-show="loading">Validando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- QR Scanner --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM6.5 6.5h.01M6.5 17.5h.01M17.5 6.5h.01"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Scanner QR Code</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-4">Use a câmera para escanear o QR Code do ingresso.</p>
                    <button @click="toggleScanner()"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm"
                            :class="scannerActive
                                ? 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200'
                                : 'bg-violet-600 text-white hover:bg-violet-700'">
                        <template x-if="!scannerActive">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </template>
                        <template x-if="scannerActive">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </template>
                        <span x-text="scannerActive ? 'Parar Scanner' : 'Iniciar Scanner'"></span>
                    </button>
                    <div id="qr-reader" class="mt-4 rounded-xl overflow-hidden"></div>
                </div>
            </div>
        </div>

        {{-- Result Display --}}
        <div x-show="result" x-cloak class="mt-6">
            <div class="rounded-2xl p-5 border"
                 :class="{
                    'bg-green-50 border-green-200': result === 'valid',
                    'bg-red-50 border-red-200': result === 'invalid' || result === 'error',
                    'bg-amber-50 border-amber-200': result === 'already_used',
                    'bg-blue-50 border-blue-200': result === 'undone' || result === 'not_checked_in'
                 }">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        {{-- Icon --}}
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             :class="{
                                'bg-green-100': result === 'valid',
                                'bg-red-100': result === 'invalid' || result === 'error',
                                'bg-amber-100': result === 'already_used',
                                'bg-blue-100': result === 'undone' || result === 'not_checked_in'
                             }">
                            <template x-if="result === 'valid'">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="result === 'invalid' || result === 'error'">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="result === 'already_used'">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </template>
                            <template x-if="result === 'undone' || result === 'not_checked_in'">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                        </div>

                        <div>
                            <p class="font-bold text-base"
                               :class="{
                                  'text-green-800': result === 'valid',
                                  'text-red-800': result === 'invalid' || result === 'error',
                                  'text-amber-800': result === 'already_used',
                                  'text-blue-800': result === 'undone' || result === 'not_checked_in'
                               }"
                               x-text="resultTitle"></p>
                            <p class="text-sm mt-0.5"
                               :class="{
                                  'text-green-700': result === 'valid',
                                  'text-red-700': result === 'invalid' || result === 'error',
                                  'text-amber-700': result === 'already_used',
                                  'text-blue-700': result === 'undone' || result === 'not_checked_in'
                               }"
                               x-text="resultMessage"></p>
                            <div x-show="participantName" class="mt-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700" x-text="participantName"></span>
                            </div>
                        </div>
                    </div>

                    <div x-show="result === 'valid'" class="flex-shrink-0">
                        <button @click="undoCheckin()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 text-xs font-semibold transition"
                                :disabled="loading">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Desfazer
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const events = @json($events->map(fn($e) => ['id' => $e->id, 'tickets_count' => $e->tickets_count, 'checked_in_count' => $e->checked_in_count]));

        function checkinApp() {
            return {
                selectedEventId: '',
                ticketCode: '',
                loading: false,
                result: null,
                resultTitle: '',
                resultMessage: '',
                participantName: '',
                lastTicketCode: '',
                checkedIn: 0,
                totalTickets: 0,
                scannerActive: false,
                html5QrCode: null,

                init() {},

                updateStats() {
                    const event = events.find(e => e.id == this.selectedEventId);
                    if (event) {
                        this.totalTickets = event.tickets_count;
                        this.checkedIn = event.checked_in_count;
                    } else {
                        this.totalTickets = 0;
                        this.checkedIn = 0;
                    }
                },

                async validateTicket(code = null) {
                    const ticketCode = code || this.ticketCode;
                    if (!ticketCode) return;

                    this.loading = true;
                    this.result = null;
                    this.lastTicketCode = ticketCode;

                    try {
                        const response = await fetch('{{ route('dashboard.checkin.validate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ ticket_code: ticketCode }),
                        });

                        const data = await response.json();
                        this.handleResult(data);

                        if (data.status === 'valid') {
                            this.checkedIn++;
                        }
                    } catch (e) {
                        this.result = 'error';
                        this.resultTitle = 'Erro';
                        this.resultMessage = 'Não foi possível conectar ao servidor.';
                    } finally {
                        this.loading = false;
                        this.ticketCode = '';
                    }
                },

                handleResult(data) {
                    this.participantName = '';
                    this.result = data.status;

                    const messages = {
                        valid:          { title: 'Check-in Realizado!', message: 'Ingresso válido. Participante confirmado.' },
                        already_used:   { title: 'Ingresso Já Utilizado', message: 'Este ingresso já foi usado anteriormente.' },
                        invalid:        { title: 'Ingresso Inválido', message: 'Ingresso não encontrado ou cancelado.' },
                        not_checked_in: { title: 'Sem Check-in', message: 'Este ingresso ainda não passou por check-in.' },
                        undone:         { title: 'Check-in Desfeito', message: 'O check-in foi revertido com sucesso.' },
                        error:          { title: 'Erro', message: 'Algo deu errado. Tente novamente.' },
                    };

                    const msg = messages[data.status] || { title: 'Desconhecido', message: '' };
                    this.resultTitle = msg.title;
                    this.resultMessage = msg.message;

                    if (data.participant) {
                        this.participantName = data.participant.name || '';
                    }
                },

                async undoCheckin() {
                    if (!this.lastTicketCode) return;

                    this.loading = true;

                    try {
                        const response = await fetch('{{ route('dashboard.checkin.undo') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ ticket_code: this.lastTicketCode }),
                        });

                        const data = await response.json();
                        this.handleResult(data);

                        if (data.status === 'undone') {
                            this.checkedIn = Math.max(0, this.checkedIn - 1);
                        }
                    } catch (e) {
                        this.result = 'error';
                        this.resultTitle = 'Erro';
                        this.resultMessage = 'Não foi possível conectar ao servidor.';
                    } finally {
                        this.loading = false;
                    }
                },

                toggleScanner() {
                    if (this.scannerActive) {
                        this.stopScanner();
                    } else {
                        this.startScanner();
                    }
                },

                startScanner() {
                    this.html5QrCode = new Html5Qrcode('qr-reader');
                    this.html5QrCode.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            this.stopScanner();
                            this.validateTicket(decodedText);
                        },
                        () => {}
                    ).then(() => {
                        this.scannerActive = true;
                    }).catch((err) => {
                        console.error('QR start error', err);
                    });
                },

                stopScanner() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop().then(() => {
                            this.html5QrCode.clear();
                            this.scannerActive = false;
                        }).catch(() => {
                            this.scannerActive = false;
                        });
                    }
                },
            };
        }
    </script>
</x-layouts.dashboard>
