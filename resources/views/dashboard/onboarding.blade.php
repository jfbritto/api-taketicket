<x-layouts.app title="Configurar Conta — TakeTicket">
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-white py-12 px-4">
        <div class="max-w-xl mx-auto">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Bem-vindo ao TakeTicket!</h1>
                <p class="text-gray-500">Olá, <strong>{{ auth()->user()->name }}</strong>! Configure seu perfil de organizador e comece a vender ingressos em minutos.</p>
            </div>

            {{-- Progress Steps --}}
            <div class="flex items-center justify-center gap-2 mb-8">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold shadow">1</div>
                    <span class="text-sm font-semibold text-indigo-700">Perfil</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-300"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <span class="text-sm text-gray-400">Evento</span>
                </div>
                <div class="w-12 h-0.5 bg-gray-300"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                    <span class="text-sm text-gray-400">Publicar</span>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Criar Perfil de Organizador</h2>
                <p class="text-sm text-gray-500 mb-6">Estas informações serão exibidas para os compradores de ingresso.</p>

                <form method="POST" action="{{ route('dashboard.storeOrganizer') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center gap-1">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Nome da Organização *
                            <x-tooltip text="Como sua empresa ou grupo será exibido publicamente para os compradores de ingresso." />
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               placeholder="Ex: Eventos Culturais SP, Academia de Dança ABC..."
                               class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-400">Use o nome pelo qual você é conhecido no mercado.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center gap-1">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            CPF ou CNPJ
                            <x-tooltip text="Pessoa física: CPF (000.000.000-00). Pessoa jurídica: CNPJ (00.000.000/0001-00). Pode ser solicitado para verificação e emissão de nota fiscal." />
                        </label>
                        <input type="text" name="document" value="{{ old('document') }}"
                               placeholder="000.000.000-00 ou 00.000.000/0001-00"
                               id="onboarding-document"
                               class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3 @error('document') border-red-500 @enderror">
                        @error('document')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center gap-1">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            Telefone / WhatsApp
                            <x-tooltip text="Número de contato para suporte e comunicações com compradores." />
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               placeholder="(11) 99999-9999"
                               id="onboarding-phone"
                               class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-4 py-3 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full bg-indigo-600 text-white py-3.5 px-4 rounded-xl hover:bg-indigo-700 font-semibold transition flex items-center justify-center gap-2">
                            Continuar — Criar meu primeiro evento
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-3">Você poderá editar estas informações depois nas configurações.</p>
                    </div>
                </form>
            </div>

            {{-- What you get --}}
            <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                <div class="bg-white rounded-xl p-4 border border-gray-100">
                    <div class="text-2xl mb-1">🎟️</div>
                    <p class="text-xs text-gray-600 font-medium">Venda ingressos online</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100">
                    <div class="text-2xl mb-1">📱</div>
                    <p class="text-xs text-gray-600 font-medium">Check-in por QR Code</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100">
                    <div class="text-2xl mb-1">📊</div>
                    <p class="text-xs text-gray-600 font-medium">Relatórios em tempo real</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var docEl = document.getElementById('onboarding-document');
        if (docEl) {
            IMask(docEl, {
                mask: [
                    { mask: '000.000.000-00', maxLength: 11 },
                    { mask: '00.000.000/0000-00' }
                ],
                dispatch: function(appended, dynamicMasked) {
                    var number = (dynamicMasked.value + appended).replace(/\D/g, '');
                    return number.length > 11 ? dynamicMasked.compiledMasks[1] : dynamicMasked.compiledMasks[0];
                }
            });
        }
        var phoneEl = document.getElementById('onboarding-phone');
        if (phoneEl) {
            IMask(phoneEl, {
                mask: [
                    { mask: '(00) 0000-0000' },
                    { mask: '(00) 00000-0000' }
                ],
                dispatch: function(appended, dynamicMasked) {
                    var number = (dynamicMasked.value + appended).replace(/\D/g, '');
                    return number.length > 10 ? dynamicMasked.compiledMasks[1] : dynamicMasked.compiledMasks[0];
                }
            });
        }
    });
    </script>
    @endpush
</x-layouts.app>
