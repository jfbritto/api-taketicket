<x-layouts.dashboard header="Edit Event">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('dashboard.events') }}" class="text-indigo-600 hover:underline text-sm">&larr; Back to Events</a>
                <h2 class="text-xl font-semibold text-gray-800 mt-2">Edit Event: {{ $event->title }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <x-badge :type="$event->status->value">{{ ucfirst($event->status->value) }}</x-badge>

                @if($event->status->value === 'draft')
                    <form method="POST" action="{{ route('dashboard.events.publish', $event) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Publish
                        </button>
                    </form>
                @endif

                @if($event->status->value !== 'cancelled')
                    <form method="POST" action="{{ route('dashboard.events.cancel', $event) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                onclick="return confirm('Cancel this event?')"
                                class="px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Cancel Event
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('dashboard.events.update', $event) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <x-card title="Event Details" class="mb-6">
                <div class="space-y-5">
                    <x-input label="Title" name="title" :value="$event->title" required />

                    <x-textarea label="Description" name="description" rows="4" :value="$event->description" />

                    <x-input label="Location" name="location" :value="$event->location" />

                    <x-input label="Address" name="address" :value="$event->address" />

                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="City" name="city" :value="$event->city" />
                        <x-input label="State (UF)" name="state" :value="$event->state" maxlength="2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Start Date" name="start_date" type="datetime-local" required
                                 :value="$event->start_date?->format('Y-m-d\TH:i')" />
                        <x-input label="End Date" name="end_date" type="datetime-local"
                                 :value="$event->end_date?->format('Y-m-d\TH:i')" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Banner Image</label>
                        @if($event->banner)
                            <img src="{{ asset('storage/' . $event->banner) }}" alt="Current banner"
                                 class="mb-2 h-24 w-auto rounded-lg object-cover">
                        @endif
                        <input type="file" name="banner" accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('banner')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Ticket Types --}}
            <x-card title="Ticket Types" class="mb-6">
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
                        <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-sm font-medium text-gray-700" x-text="'Ticket #' + (index + 1)"></span>
                                <button type="button" @click="removeTicket(index)"
                                        class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                            </div>

                            <input type="hidden" :name="'ticket_types[' + index + '][id]'" :value="ticket.id">

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                                    <input type="text" :name="'ticket_types[' + index + '][name]'" x-model="ticket.name"
                                           required class="w-full rounded border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Price (R$) *</label>
                                    <input type="number" :name="'ticket_types[' + index + '][price]'" x-model="ticket.price"
                                           step="0.01" min="0" required class="w-full rounded border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Quantity *</label>
                                    <input type="number" :name="'ticket_types[' + index + '][quantity]'" x-model="ticket.quantity"
                                           min="1" required class="w-full rounded border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Sale Start *</label>
                                    <input type="datetime-local" :name="'ticket_types[' + index + '][sale_start]'" x-model="ticket.sale_start"
                                           required class="w-full rounded border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Sale End *</label>
                                    <input type="datetime-local" :name="'ticket_types[' + index + '][sale_end]'" x-model="ticket.sale_end"
                                           required class="w-full rounded border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addTicket()"
                            class="mt-2 w-full border-2 border-dashed border-gray-300 rounded-lg py-3 text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600">
                        + Add Ticket Type
                    </button>
                </div>
            </x-card>

            {{-- Custom Fields --}}
            <x-card title="Custom Fields" class="mb-6">
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
                        <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <span class="text-sm font-medium text-gray-700" x-text="'Field #' + (index + 1)"></span>
                                <button type="button" @click="removeField(index)"
                                        class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                            </div>

                            <input type="hidden" :name="'custom_fields[' + index + '][id]'" :value="field.id">
                            <input type="hidden" :name="'custom_fields[' + index + '][position]'" :value="index">

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Label *</label>
                                    <input type="text" :name="'custom_fields[' + index + '][label]'" x-model="field.label"
                                           required class="w-full rounded border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                                    <select :name="'custom_fields[' + index + '][type]'" x-model="field.type"
                                            class="w-full rounded border-gray-300 shadow-sm text-sm px-3 py-2 border">
                                        <option value="text">Text</option>
                                        <option value="number">Number</option>
                                        <option value="select">Select</option>
                                        <option value="checkbox">Checkbox</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" :name="'custom_fields[' + index + '][required]'"
                                           x-model="field.required" value="1"
                                           class="rounded border-gray-300 text-indigo-600">
                                    Required
                                </label>
                            </div>

                            <div x-show="field.type === 'select'" class="mt-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Options (comma-separated)</label>
                                <input type="text" :name="'custom_fields[' + index + '][options]'" x-model="field.options"
                                       placeholder="Option 1, Option 2, Option 3"
                                       class="w-full rounded border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addField()"
                            class="mt-2 w-full border-2 border-dashed border-gray-300 rounded-lg py-3 text-sm text-gray-500 hover:border-indigo-400 hover:text-indigo-600">
                        + Add Custom Field
                    </button>
                </div>
            </x-card>

            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard.events') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.dashboard>
