<x-layouts.dashboard header="Check-in">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div x-data="checkinApp()" x-init="init()">

        {{-- Event Selector --}}
        <x-card class="mb-6">
            <div class="flex items-center gap-4">
                <label for="event-select" class="text-sm font-medium text-gray-700 whitespace-nowrap">Select Event:</label>
                <select id="event-select" x-model="selectedEventId" @change="updateStats()"
                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border text-sm">
                    <option value="">-- Choose an event --</option>
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
            <div x-show="selectedEventId" x-cloak class="mt-4 flex gap-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-600" x-text="checkedIn"></p>
                    <p class="text-xs text-gray-500">Checked In</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900" x-text="totalTickets"></p>
                    <p class="text-xs text-gray-500">Total Tickets</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600" x-text="totalTickets > 0 ? Math.round((checkedIn / totalTickets) * 100) + '%' : '0%'"></p>
                    <p class="text-xs text-gray-500">Rate</p>
                </div>
            </div>
        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Manual Check-in --}}
            <x-card title="Manual Check-in">
                <form @submit.prevent="validateTicket()">
                    <div class="flex gap-2">
                        <input type="text" x-model="ticketCode" placeholder="Enter ticket code"
                               class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border text-sm"
                               :disabled="loading" />
                        <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50 text-sm font-medium"
                                :disabled="loading || !ticketCode">
                            <span x-show="!loading">Check In</span>
                            <span x-show="loading">...</span>
                        </button>
                    </div>
                </form>
            </x-card>

            {{-- QR Scanner --}}
            <x-card title="QR Code Scanner">
                <div class="flex flex-col gap-3">
                    <button @click="toggleScanner()"
                            class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 text-sm font-medium"
                            x-text="scannerActive ? 'Stop Scanner' : 'Start Scanner'">
                    </button>
                    <div id="qr-reader" class="rounded-lg overflow-hidden"></div>
                </div>
            </x-card>
        </div>

        {{-- Result Display --}}
        <div x-show="result" x-cloak class="mt-6">
            <div :class="{
                    'bg-green-50 border border-green-200 text-green-800': result === 'valid',
                    'bg-red-50 border border-red-200 text-red-800': result === 'invalid' || result === 'not_checked_in' || result === 'error',
                    'bg-yellow-50 border border-yellow-200 text-yellow-800': result === 'already_used',
                    'bg-blue-50 border border-blue-200 text-blue-800': result === 'undone'
                 }"
                 class="rounded-lg p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-base" x-text="resultTitle"></p>
                        <p class="text-sm mt-1" x-text="resultMessage"></p>
                        <div x-show="participantName" class="mt-2 text-sm">
                            <span class="font-medium">Participant:</span> <span x-text="participantName"></span>
                        </div>
                    </div>
                    <div x-show="result === 'valid'" class="ml-4 flex-shrink-0">
                        <button @click="undoCheckin()"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1.5 rounded-lg font-medium"
                                :disabled="loading">
                            Undo Check-in
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

                init() {
                    // Nothing to init
                },

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
                        this.resultTitle = 'Error';
                        this.resultMessage = 'Could not connect to server.';
                    } finally {
                        this.loading = false;
                        this.ticketCode = '';
                    }
                },

                handleResult(data) {
                    this.participantName = '';
                    this.result = data.status;

                    const messages = {
                        valid: { title: 'Check-in Successful!', message: 'Ticket is valid and has been checked in.' },
                        already_used: { title: 'Already Checked In', message: 'This ticket has already been used.' },
                        invalid: { title: 'Invalid Ticket', message: 'Ticket not found or is cancelled.' },
                        not_checked_in: { title: 'Not Checked In', message: 'This ticket has not been checked in yet.' },
                        undone: { title: 'Check-in Undone', message: 'The check-in has been reversed successfully.' },
                        error: { title: 'Error', message: 'Something went wrong.' },
                    };

                    const msg = messages[data.status] || { title: 'Unknown', message: '' };
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
                        this.resultTitle = 'Error';
                        this.resultMessage = 'Could not connect to server.';
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
