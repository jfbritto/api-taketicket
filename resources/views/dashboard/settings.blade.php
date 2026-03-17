<x-layouts.dashboard header="Configurações">
    <div class="max-w-3xl mx-auto">

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Configurações</h2>
            <p class="text-gray-500 mt-1">Gerencie os dados do seu perfil e da sua conta.</p>
        </div>

        {{-- Organizer Profile --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900">Perfil do Organizador</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('dashboard.settings.organizer') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                            @if($organizer->logo)
                                <img src="{{ asset('storage/' . $organizer->logo) }}" alt="Logo"
                                     class="mb-3 h-16 w-16 rounded-xl object-cover border border-gray-100 shadow-sm">
                            @endif
                            <input type="file" name="logo" accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                            @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <x-input label="Nome do Organizador" name="name" :value="$organizer->name" required />
                        <x-textarea label="Descrição" name="description" rows="3" :value="$organizer->description" />

                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="Telefone" name="phone" :value="$organizer->phone" class="mask-phone" />
                            <x-input label="CNPJ / CPF" name="document" :value="$organizer->document" class="mask-cnpj" />
                        </div>

                        <x-input label="Endereço" name="address" :value="$organizer->address" />

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <x-input label="Cidade" name="city" :value="$organizer->city" />
                            </div>
                            <x-input label="UF" name="state" :value="$organizer->state" maxlength="2" />
                        </div>

                        <x-input label="CEP" name="postal_code" :value="$organizer->postal_code" class="mask-cep" />

                        <div class="rounded-xl border p-4 {{ $organizer->asaas_account_id ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200' }}">
                            <div class="flex items-center gap-2">
                                @if($organizer->asaas_account_id)
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-green-700">Conta de pagamentos conectada</span>
                                @else
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-amber-700">Conta de pagamentos pendente</span>
                                @endif
                            </div>
                            <p class="text-xs mt-1 {{ $organizer->asaas_account_id ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $organizer->asaas_account_id ? 'Pagamentos e repasses estão habilitados.' : 'A conta será criada automaticamente ao publicar seu primeiro evento.' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Salvar Perfil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900">Alterar Senha</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('dashboard.settings.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <x-input label="Senha Atual" name="current_password" type="password" />
                        <x-input label="Nova Senha" name="new_password" type="password" />
                        <x-input label="Confirmar Nova Senha" name="new_password_confirmation" type="password" />
                    </div>
                    <div class="flex justify-end mt-6">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Alterar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.mask-phone').forEach(el => {
            IMask(el, { mask: '(00) 00000-0000' });
        });
        document.querySelectorAll('.mask-cnpj').forEach(el => {
            IMask(el, {
                mask: [{ mask: '000.000.000-00' }, { mask: '00.000.000/0000-00' }],
                dispatch: (appended, dynamicMasked) => {
                    const val = (dynamicMasked.value + appended).replace(/\D/g, '');
                    return dynamicMasked.compiledMasks[val.length > 11 ? 1 : 0];
                }
            });
        });
        document.querySelectorAll('.mask-cep').forEach(el => {
            IMask(el, { mask: '00000-000' });
        });
    </script>
    @endpush
</x-layouts.dashboard>
