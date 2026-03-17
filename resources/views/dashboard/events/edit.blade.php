<x-layouts.dashboard header="Editar Evento">
    <div class="max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('dashboard.events') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-indigo-600 transition mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar para Eventos
            </a>
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h2>
                    <p class="text-gray-500 mt-1">Edite as informações do seu evento.</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @php
                        $statusLabels = ['draft' => 'Rascunho', 'published' => 'Publicado', 'cancelled' => 'Cancelado'];
                    @endphp
                    <x-badge :type="$event->status->value">{{ $statusLabels[$event->status->value] ?? ucfirst($event->status->value) }}</x-badge>

                    @if($event->status->value === 'draft')
                        <form method="POST" action="{{ route('dashboard.events.publish', $event) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Publicar
                            </button>
                        </form>
                    @endif

                    @if($event->status->value !== 'cancelled')
                        <form method="POST" action="{{ route('dashboard.events.cancel', $event) }}" id="form-cancel-event">
                            @csrf
                            @method('PATCH')
                            <button type="button" onclick="confirmCancelEvent()"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl transition shadow-sm"
                                    style="background-color: #ef4444; color: #ffffff;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar Evento
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('dashboard.events.update', $event) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Detalhes do Evento</h3>
                </div>
                <div class="p-6 space-y-5">
                    <x-input label="Título" name="title" :value="$event->title" required />
                    <x-textarea label="Descrição" name="description" rows="4" :value="$event->description" />
                    <x-input label="Local / Venue" name="location" :value="$event->location" />
                    <x-input label="Endereço" name="address" :value="$event->address" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Cidade" name="city" :value="$event->city" />
                        <x-input label="UF" name="state" :value="$event->state" maxlength="2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Data/Hora de Início" name="start_date" type="datetime-local" required
                                 :value="$event->start_date?->format('Y-m-d\TH:i')" />
                        <x-input label="Data/Hora de Término" name="end_date" type="datetime-local"
                                 :value="$event->end_date?->format('Y-m-d\TH:i')" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imagem de Capa</label>
                        @if($event->banner)
                            <img src="{{ asset('storage/' . $event->banner) }}" alt="Banner atual"
                                 class="mb-3 h-28 w-auto rounded-xl object-cover border border-gray-100 shadow-sm">
                        @endif
                        <input type="file" name="banner" accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        @error('banner')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Ticket Types --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Tipos de Ingresso</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-5">Configure os lotes de ingressos. Cada lote pode ter preço, quantidade e período de venda distintos.</p>
                    <div x-data="{
                        tickets: {{ Js::from($event->ticketTypes->map(fn($t) => [
                            'id' => $t->id,
                            'name' => $t->name,
                            'price' => $t->price,
                            'quantity' => $t->quantity,
                            'sale_start' => $t->sale_start?->format('Y-m-d\TH:i') ?? '',
                            'sale_end' => $t->sale_end?->format('Y-m-d\TH:i') ?? '',
                        ])) }},
                        addTicket() {
                            this.tickets.push({ id: null, name: '', price: 0, quantity: 1, sale_start: '', sale_end: '' });
                        },
                        removeTicket(index) {
                            this.tickets.splice(index, 1);
                        }
                    }">
                        <template x-for="(ticket, index) in tickets" :key="index">
                            <div class="border border-gray-100 rounded-xl p-5 mb-4 bg-gray-50/50">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700" x-text="'Ingresso #' + (index + 1)"></span>
                                    </div>
                                    <button type="button" @click="removeTicket(index)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-medium transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Remover
                                    </button>
                                </div>

                                <input type="hidden" :name="'ticket_types[' + index + '][id]'" :value="ticket.id">

                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nome *</label>
                                        <input type="text" :name="'ticket_types[' + index + '][name]'" x-model="ticket.name"
                                               required class="w-full rounded-xl border-gray-200 shadow-sm text-sm px-3 py-2 border focus:border-indigo-400 focus:ring-indigo-400">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Valor (R$) *</label>
                                        <input type="number" :name="'ticket_types[' + index + '][price]'" x-model="ticket.price"
                                               step="0.01" min="0" required class="w-full rounded-xl border-gray-200 shadow-sm text-sm px-3 py-2 border focus:border-indigo-400 focus:ring-indigo-400">
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Quantidade *</label>
                                        <input type="number" :name="'ticket_types[' + index + '][quantity]'" x-model="ticket.quantity"
                                               min="1" required class="w-full rounded-xl border-gray-200 shadow-sm text-sm px-3 py-2 border focus:border-indigo-400 focus:ring-indigo-400">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Início das Vendas *</label>
                                        <input type="datetime-local" :name="'ticket_types[' + index + '][sale_start]'" x-model="ticket.sale_start"
                                               required class="w-full rounded-xl border-gray-200 shadow-sm text-sm px-3 py-2 border focus:border-indigo-400 focus:ring-indigo-400">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fim das Vendas *</label>
                                        <input type="datetime-local" :name="'ticket_types[' + index + '][sale_end]'" x-model="ticket.sale_end"
                                               required class="w-full rounded-xl border-gray-200 shadow-sm text-sm px-3 py-2 border focus:border-indigo-400 focus:ring-indigo-400">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addTicket()"
                                class="w-full border-2 border-dashed border-gray-200 rounded-xl py-4 text-sm text-gray-400 hover:border-indigo-400 hover:text-indigo-600 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Tipo de Ingresso
                        </button>
                    </div>
                </div>
            </div>

            {{-- Custom Fields --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900">Campos Personalizados</h3>
                </div>
                <div class="p-6">
                    <div x-data="{
                        fields: {{ Js::from($event->customFields->map(fn($f) => [
                            'id' => $f->id,
                            'label' => $f->label,
                            'type' => $f->type,
                            'required' => $f->required,
                            'options' => is_array($f->options) ? implode(', ', $f->options) : ($f->options ?? ''),
                            'position' => $f->position,
                        ])) }},
                        addField() {
                            this.fields.push({ id: null, label: '', type: 'text', required: false, options: '', position: this.fields.length });
                        },
                        removeField(index) {
                            this.fields.splice(index, 1);
                        }
                    }">
                        <template x-for="(field, index) in fields" :key="index">
                            <div class="border border-gray-100 rounded-xl p-5 mb-4 bg-gray-50/50">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-violet-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700" x-text="'Campo #' + (index + 1)"></span>
                                    </div>
                                    <button type="button" @click="removeField(index)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-xs font-medium transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Remover
                                    </button>
                                </div>

                                <input type="hidden" :name="'custom_fields[' + index + '][id]'" :value="field.id">
                                <input type="hidden" :name="'custom_fields[' + index + '][position]'" :value="index">

                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Rótulo *</label>
                                        <input type="text" :name="'custom_fields[' + index + '][label]'" x-model="field.label"
                                               required class="w-full rounded-xl border-gray-200 shadow-sm text-sm px-3 py-2 border focus:border-indigo-400 focus:ring-indigo-400">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tipo *</label>
                                        <select :name="'custom_fields[' + index + '][type]'" x-model="field.type"
                                                class="w-full rounded-xl border-gray-200 shadow-sm text-sm px-3 py-2 border focus:border-indigo-400 focus:ring-indigo-400">
                                            <option value="text">Texto</option>
                                            <option value="number">Número</option>
                                            <option value="select">Seleção</option>
                                            <option value="checkbox">Caixa de verificação</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                        <input type="checkbox" :name="'custom_fields[' + index + '][required]'"
                                               x-model="field.required" value="1"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        Obrigatório
                                    </label>
                                </div>

                                <div x-show="field.type === 'select'" class="mt-3">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Opções (separadas por vírgula)</label>
                                    <input type="text" :name="'custom_fields[' + index + '][options]'" x-model="field.options"
                                           placeholder="Opção 1, Opção 2, Opção 3"
                                           class="w-full rounded-xl border-gray-200 shadow-sm text-sm px-3 py-2 border focus:border-indigo-400 focus:ring-indigo-400">
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addField()"
                                class="w-full border-2 border-dashed border-gray-200 rounded-xl py-4 text-sm text-gray-400 hover:border-violet-400 hover:text-violet-600 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Campo
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="flex justify-between items-center">
                <a href="{{ route('dashboard.events') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Salvar Alterações
                </button>
            </div>
        </form>

    </div>
@push('scripts')
<script>
function confirmCancelEvent() {
    Swal.fire({
        title: 'Cancelar evento?',
        text: 'Esta ação não pode ser desfeita. O evento será marcado como cancelado.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sim, cancelar evento',
        cancelButtonText: 'Voltar',
        borderRadius: '1rem',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-cancel-event').submit();
        }
    });
}
</script>
@endpush
</x-layouts.dashboard>
