<x-layouts.dashboard header="Configurações">
    <div x-data="{ tab: '{{ $errors->has('current_password') || $errors->has('new_password') ? 'security' : 'profile' }}' }">
        <div class="flex gap-8">

            {{-- Left nav --}}
            <div class="w-56 flex-shrink-0">
                {{-- Account --}}
                <div class="flex items-center gap-3 mb-6 px-1">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ explode(' ', auth()->user()->name)[0] }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                {{-- Nav --}}
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Conta</p>
                <nav class="space-y-0.5">
                    <button @click="tab = 'profile'" type="button"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition font-medium text-left border-l-2"
                            :class="tab === 'profile' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Perfil
                    </button>
                    <button @click="tab = 'security'" type="button"
                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition font-medium text-left border-l-2"
                            :class="tab === 'security' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-800 hover:bg-gray-50'">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Segurança
                    </button>
                </nav>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">

                {{-- Profile tab --}}
                <div x-show="tab === 'profile'" x-cloak>
                    <form method="POST" action="{{ route('dashboard.settings.organizer') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Top action bar --}}
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Perfil do Organizador</h2>
                                <p class="text-xs text-gray-500 mt-0.5">Informações públicas do seu perfil</p>
                            </div>
                            <button type="submit"
                                    style="background-color:#111827;color:#ffffff;" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm"
                                Salvar alterações
                            </button>
                        </div>

                        {{-- Logo section --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-4">
                            <div class="px-6 py-5 flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">Logo do Organizador</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">PNG, JPG ou GIF — máx. 2 MB</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if($organizer->logo)
                                            <img src="{{ asset('storage/' . $organizer->logo) }}" alt="Logo" id="logo-preview"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <svg id="logo-placeholder" class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <img id="logo-preview" class="w-full h-full object-cover hidden" alt="Preview">
                                        @endif
                                    </div>
                                    <label for="logo-input"
                                           class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        Alterar logo
                                    </label>
                                    <input id="logo-input" type="file" name="logo" accept="image/*" class="hidden">
                                    @error('logo')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Info section --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-4">
                            <div class="px-6 pt-5 pb-1">
                                <h3 class="text-sm font-semibold text-gray-900">Informações do Organizador</h3>
                            </div>
                            <div class="px-6 py-5 space-y-4">
                                <x-input label="Nome do Organizador" name="name" :value="$organizer->name" placeholder="Ex: João Silva Produções" required />
                                <x-textarea label="Descrição" name="description" rows="3" :value="$organizer->description" placeholder="Fale um pouco sobre sua empresa ou projeto..." />
                                <div class="grid grid-cols-2 gap-4">
                                    <x-input label="Telefone" name="phone" :value="$organizer->phone" class="mask-phone" placeholder="(11) 99999-9999" />
                                    <x-input label="CNPJ / CPF" name="document" :value="$organizer->document" class="mask-cnpj" placeholder="00.000.000/0000-00" />
                                </div>
                            </div>
                        </div>

                        {{-- Address section --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-4">
                            <div class="px-6 pt-5 pb-1">
                                <h3 class="text-sm font-semibold text-gray-900">Endereço</h3>
                            </div>
                            <div class="px-6 py-5 space-y-4">
                                <x-input label="Logradouro" name="address" :value="$organizer->address" placeholder="Av. Paulista, 1000" />
                                <div class="grid grid-cols-6 gap-4">
                                    <div class="col-span-3">
                                        <x-input label="Cidade" name="city" :value="$organizer->city" placeholder="São Paulo" />
                                    </div>
                                    <div class="col-span-1">
                                        <x-input label="UF" name="state" :value="$organizer->state" maxlength="2" placeholder="SP" />
                                    </div>
                                    <div class="col-span-2">
                                        <x-input label="CEP" name="postal_code" :value="$organizer->postal_code" class="mask-cep" placeholder="00000-000" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Payments status --}}
                        @if(!$organizer->asaas_account_id)
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 flex items-start gap-3 mb-4">
                                <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800">Conta de pagamentos pendente</p>
                                    <p class="text-xs text-amber-700 mt-0.5">Será criada automaticamente ao publicar seu primeiro evento.</p>
                                </div>
                            </div>
                        @endif

                    </form>
                </div>

                {{-- Security tab --}}
                <div x-show="tab === 'security'" x-cloak>
                    <form method="POST" action="{{ route('dashboard.settings.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Segurança</h2>
                                <p class="text-xs text-gray-500 mt-0.5">Use no mínimo 8 caracteres com letras e números.</p>
                            </div>
                            <button type="submit"
                                    style="background-color:#111827;color:#ffffff;" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold transition shadow-sm"
                                Atualizar senha
                            </button>
                        </div>

                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                            <div class="px-6 py-5 space-y-4">
                                <x-input label="Senha atual" name="current_password" type="password" placeholder="••••••••" />
                                <div class="grid grid-cols-2 gap-4">
                                    <x-input label="Nova senha" name="new_password" type="password" placeholder="Mínimo 8 caracteres" />
                                    <x-input label="Confirmar nova senha" name="new_password_confirmation" type="password" placeholder="Repita a nova senha" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
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
