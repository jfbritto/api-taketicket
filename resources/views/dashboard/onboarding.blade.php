<x-layouts.app title="Criar Perfil de Organizador — TakeTicket">
    <div class="max-w-md mx-auto mt-16 px-4">
        <x-card title="Criar Perfil de Organizador">
            <p class="text-gray-600 mb-6 text-sm">Configure seu perfil para começar a criar e vender ingressos para seus eventos.</p>
            <form method="POST" action="{{ route('dashboard.storeOrganizer') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Nome da Organização *
                        <x-tooltip text="Nome da empresa, grupo ou pessoa responsável pelos eventos. Este nome será exibido publicamente para os compradores." />
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ex: Eventos Culturais SP"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Documento (CPF ou CNPJ)
                        <x-tooltip text="Informe o CPF (pessoa física) ou CNPJ (pessoa jurídica) do organizador. Pode ser solicitado para verificação de identidade e emissão de nota fiscal." />
                    </label>
                    <input type="text" name="document" value="{{ old('document') }}"
                           placeholder="000.000.000-00 ou 00.000.000/0000-00"
                           id="onboarding-document"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('document') border-red-500 @enderror">
                    @error('document')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                        Telefone / WhatsApp
                        <x-tooltip text="Número de contato para suporte e comunicações. Pode ser exibido para os participantes do evento." />
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           placeholder="(11) 99999-9999"
                           id="onboarding-phone"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2.5 px-4 rounded-lg hover:bg-indigo-700 font-medium transition">
                    Criar Perfil
                </button>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // CPF/CNPJ dynamic mask
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
        // Phone mask
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
