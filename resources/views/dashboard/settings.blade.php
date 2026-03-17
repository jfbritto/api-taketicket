<x-layouts.dashboard header="Configurações">
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Account Info Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-900 text-lg">{{ auth()->user()->name }}</p>
                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
            </div>
            <div class="ml-auto">
                @if($organizer->asaas_account_id)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pagamentos ativos
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Pagamentos pendentes
                    </span>
                @endif
            </div>
        </div>

        {{-- Organizer Profile --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Perfil do Organizador</h3>
                    <p class="text-xs text-gray-500">Informações públicas do seu perfil de organizador</p>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.settings.organizer') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Logo Upload --}}
                <div class="px-6 pt-6 pb-4 border-b border-gray-50">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Logo do Organizador</label>
                    <div class="flex items-center gap-5">
                        <div class="w-20 h-20 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0" id="logo-preview-container">
                            @if($organizer->logo)
                                <img src="{{ asset('storage/' . $organizer->logo) }}" alt="Logo" id="logo-preview"
                                     class="w-full h-full object-cover rounded-2xl">
                            @else
                                <svg id="logo-placeholder" class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <img id="logo-preview" class="w-full h-full object-cover rounded-2xl hidden" alt="Preview">
                            @endif
                        </div>
                        <div>
                            <label for="logo-input"
                                   class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Escolher imagem
                            </label>
                            <input id="logo-input" type="file" name="logo" accept="image/*" class="hidden">
                            <p class="text-xs text-gray-400 mt-1.5">PNG, JPG ou GIF — máx. 2 MB</p>
                            @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Fields --}}
                <div class="p-6 space-y-5">
                    <x-input label="Nome do Organizador" name="name" :value="$organizer->name" required />
                    <x-textarea label="Descrição" name="description" rows="3" :value="$organizer->description" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Telefone" name="phone" :value="$organizer->phone" class="mask-phone" />
                        <x-input label="CNPJ / CPF" name="document" :value="$organizer->document" class="mask-cnpj" />
                    </div>

                    <x-input label="Endereço" name="address" :value="$organizer->address" />

                    <div class="grid grid-cols-6 gap-4">
                        <div class="col-span-3">
                            <x-input label="Cidade" name="city" :value="$organizer->city" />
                        </div>
                        <div class="col-span-1">
                            <x-input label="UF" name="state" :value="$organizer->state" maxlength="2" />
                        </div>
                        <div class="col-span-2">
                            <x-input label="CEP" name="postal_code" :value="$organizer->postal_code" class="mask-cep" />
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
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

        {{-- Change Password --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Segurança</h3>
                    <p class="text-xs text-gray-500">Altere sua senha de acesso</p>
                </div>
            </div>

            <form method="POST" action="{{ route('dashboard.settings.password') }}">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-4">
                    <x-input label="Senha Atual" name="current_password" type="password" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Nova Senha" name="new_password" type="password" />
                        <x-input label="Confirmar Nova Senha" name="new_password_confirmation" type="password" />
                    </div>

                    <p class="text-xs text-gray-400">Use no mínimo 8 caracteres com letras e números.</p>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
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

    @push('scripts')
    <script>
        // Logo preview
        document.getElementById('logo-input')?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                const preview = document.getElementById('logo-preview');
                const placeholder = document.getElementById('logo-placeholder');
                preview.src = ev.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });

        // Input masks
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
