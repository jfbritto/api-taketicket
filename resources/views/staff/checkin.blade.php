<x-layouts.staff :event="$event">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div x-data="checkinApp()" x-init="init()">

        {{-- Header --}}
        <div style="margin-bottom:32px;">
            <h1 style="font-size:24px;font-weight:800;color:#111827;margin:0 0 8px 0;">Check-in</h1>
            <p style="font-size:15px;color:#6b7280;margin:0;">Valide ingressos manualmente ou por leitura de QR Code.</p>
        </div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;padding:20px;display:flex;align-items:center;gap:16px;">
                <div style="width:48px;height:48px;background:#e0e7ff;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="24" height="24" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:24px;font-weight:800;color:#111827;margin:0;">{{ $total }}</p>
                    <p style="font-size:12px;font-weight:500;color:#6b7280;margin:0;">Total de ingressos</p>
                </div>
            </div>
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;padding:20px;display:flex;align-items:center;gap:16px;">
                <div style="width:48px;height:48px;background:#d1fae5;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="24" height="24" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:24px;font-weight:800;color:#111827;margin:0;" x-text="checkedIn">{{ $checkedIn }}</p>
                    <p style="font-size:12px;font-weight:500;color:#6b7280;margin:0;">Check-ins realizados</p>
                </div>
            </div>
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;padding:20px;display:flex;align-items:center;gap:16px;">
                <div style="width:48px;height:48px;background:#fef3c7;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="24" height="24" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:24px;font-weight:800;color:#111827;margin:0;" x-text="remaining">{{ $total - $checkedIn }}</p>
                    <p style="font-size:12px;font-weight:500;color:#6b7280;margin:0;">Faltam</p>
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">

            {{-- Manual Check-in --}}
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:16px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;">
                    <div style="width:32px;height:32px;background:#e0e7ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">Check-in Manual</h3>
                </div>
                <div style="padding:24px;">
                    <p style="font-size:14px;color:#6b7280;margin:0 0 16px 0;">Digite o código do ingresso para validar.</p>
                    <form @submit.prevent="submitValidate()">
                        <div style="display:flex;gap:8px;">
                            <input type="text" x-model="ticketCode"
                                   placeholder="Ex: TKT-XXXXXXXX"
                                   style="flex:1;border-radius:10px;border:1px solid #d1d5db;padding:10px 16px;font-size:14px;font-family:monospace;outline:none;"
                                   :disabled="loading" />
                            <button type="submit"
                                    style="display:inline-flex;align-items:center;gap:8px;background:#4f46e5;color:white;padding:10px 20px;border-radius:10px;border:none;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;"
                                    :disabled="loading || !ticketCode">
                                <svg x-show="!loading" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <svg x-show="loading" width="16" height="16" fill="none" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:0.25;"></circle>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity:0.75;"></path>
                                </svg>
                                <span x-show="!loading">Validar</span>
                                <span x-show="loading">Validando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- QR Scanner --}}
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;">
                <div style="padding:16px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px;">
                    <div style="width:32px;height:32px;background:#ede9fe;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM6.5 6.5h.01M6.5 17.5h.01M17.5 6.5h.01"/>
                        </svg>
                    </div>
                    <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">Scanner QR Code</h3>
                </div>
                <div style="padding:24px;">
                    <p style="font-size:14px;color:#6b7280;margin:0 0 16px 0;">Use a câmera para escanear o QR Code do ingresso.</p>
                    <button @click="toggleScanner()"
                            style="width:100%;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;border:none;font-family:inherit;transition:background 0.2s;"
                            :style="scannerActive ? 'background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;' : 'background:#7c3aed;color:white;'">
                        <template x-if="!scannerActive">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </template>
                        <template x-if="scannerActive">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </template>
                        <span x-text="scannerActive ? 'Parar Scanner' : 'Iniciar Scanner'"></span>
                    </button>
                    <div id="qr-reader" style="margin-top:16px;border-radius:10px;overflow:hidden;"></div>
                </div>
            </div>
        </div>

        {{-- Result Display --}}
        <div x-show="result" x-cloak style="margin-top:24px;">
            <div style="border-radius:16px;padding:20px;border:1px solid;"
                 :style="{
                    'background': result === 'valid' ? '#f0fdf4' : (result === 'already_used' ? '#fffbeb' : (result === 'undone' || result === 'not_checked_in' ? '#eff6ff' : '#fef2f2')),
                    'border-color': result === 'valid' ? '#bbf7d0' : (result === 'already_used' ? '#fde68a' : (result === 'undone' || result === 'not_checked_in' ? '#bfdbfe' : '#fecaca'))
                 }">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        {{-- Icon --}}
                        <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"
                             :style="{
                                'background': result === 'valid' ? '#dcfce7' : (result === 'already_used' ? '#fef3c7' : (result === 'undone' || result === 'not_checked_in' ? '#dbeafe' : '#fee2e2'))
                             }">
                            <template x-if="result === 'valid'">
                                <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="result === 'invalid' || result === 'error'">
                                <svg width="20" height="20" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                            <template x-if="result === 'already_used'">
                                <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </template>
                            <template x-if="result === 'undone' || result === 'not_checked_in'">
                                <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </template>
                        </div>

                        <div>
                            <p style="font-size:16px;font-weight:700;margin:0 0 4px 0;"
                               :style="{
                                  'color': result === 'valid' ? '#166534' : (result === 'already_used' ? '#92400e' : (result === 'undone' || result === 'not_checked_in' ? '#1e40af' : '#991b1b'))
                               }"
                               x-text="resultTitle"></p>
                            <p style="font-size:14px;margin:0;"
                               :style="{
                                  'color': result === 'valid' ? '#15803d' : (result === 'already_used' ? '#b45309' : (result === 'undone' || result === 'not_checked_in' ? '#1d4ed8' : '#b91c1c'))
                               }"
                               x-text="resultMessage"></p>
                            <div x-show="participantName" style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                                <svg width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span style="font-size:14px;font-weight:500;color:#374151;" x-text="participantName"></span>
                            </div>
                            <div x-show="ticketCodeDisplay" style="margin-top:4px;display:flex;align-items:center;gap:8px;">
                                <svg width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                <span style="font-size:13px;color:#6b7280;font-family:monospace;" x-text="ticketCodeDisplay"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Undo button with 30s timer --}}
                    <div x-show="result === 'valid'" style="flex-shrink:0;">
                        <button @click="submitUndo()"
                                style="display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:8px;background:#fef3c7;color:#b45309;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;"
                                :disabled="loading">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <span>Desfazer</span>
                            <span x-show="undoTimer > 0" x-text="'(' + undoTimer + 's)'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        [x-cloak] { display: none !important; }
    </style>

    <script>
        const staffCheckinData = {
            total: {{ $total }},
            checkedIn: {{ $checkedIn }},
            validateUrl: '{{ route('staff.checkin.validate', $event) }}',
            undoUrl: '{{ route('staff.checkin.undo', $event) }}',
        };

        function checkinApp() {
            return {
                ticketCode: '',
                loading: false,
                result: null,
                resultTitle: '',
                resultMessage: '',
                participantName: '',
                ticketCodeDisplay: '',
                lastTicketCode: '',
                checkedIn: staffCheckinData.checkedIn,
                remaining: staffCheckinData.total - staffCheckinData.checkedIn,
                scannerActive: false,
                html5QrCode: null,
                undoTimer: 0,
                undoTimerInterval: null,

                init() {},

                startUndoTimer() {
                    this.undoTimer = 30;
                    if (this.undoTimerInterval) clearInterval(this.undoTimerInterval);
                    this.undoTimerInterval = setInterval(() => {
                        this.undoTimer--;
                        if (this.undoTimer <= 0) {
                            clearInterval(this.undoTimerInterval);
                            this.undoTimerInterval = null;
                            // Auto-hide undo by clearing result only if still valid
                            if (this.result === 'valid') {
                                this.result = null;
                            }
                        }
                    }, 1000);
                },

                async submitValidate(code = null) {
                    const ticketCode = code || this.ticketCode;
                    if (!ticketCode) return;

                    this.loading = true;
                    this.result = null;
                    this.lastTicketCode = ticketCode;
                    if (this.undoTimerInterval) clearInterval(this.undoTimerInterval);

                    try {
                        const response = await fetch(staffCheckinData.validateUrl, {
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
                            this.remaining = Math.max(0, this.remaining - 1);
                            this.startUndoTimer();
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
                    this.ticketCodeDisplay = '';
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
                        this.ticketCodeDisplay = data.participant.ticket_code || '';
                    }
                },

                async submitUndo() {
                    if (!this.lastTicketCode) return;

                    this.loading = true;
                    if (this.undoTimerInterval) clearInterval(this.undoTimerInterval);
                    this.undoTimer = 0;

                    try {
                        const response = await fetch(staffCheckinData.undoUrl, {
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
                            this.remaining++;
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
                            this.submitValidate(decodedText);
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
</x-layouts.staff>
