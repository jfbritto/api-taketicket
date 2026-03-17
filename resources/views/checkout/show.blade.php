<x-layouts.checkout :title="'Finalizar Compra — ' . $order->event->title">
    <div class="max-w-5xl mx-auto px-4 py-8" x-data
        @order-expired.window="window.location.href = '/event/{{ $order->event->slug }}'">

        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Informações dos Participantes</h1>
            <x-countdown :expires-at="$order->expires_at->toIso8601String()" />
        </div>

        <p class="text-sm text-gray-500 mb-6">Preencha os dados de cada participante. As informações serão impressas no ingresso e usadas para check-in no evento.</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Participant Forms --}}
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('checkout.participants', $order) }}">
                    @csrf

                    @php $index = 0; @endphp
                    @foreach($order->items as $item)
                        @foreach($tickets->where('order_item_id', $item->id) as $ticket)
                            <div class="bg-white rounded-lg shadow-sm border p-6 mb-4">
                                <h3 class="font-semibold text-gray-800 mb-1">
                                    Participante {{ ++$index }} — {{ $item->ticketType->name }}
                                </h3>
                                <p class="text-xs text-gray-400 mb-4">Todos os campos marcados com * são obrigatórios.</p>

                                <input type="hidden" name="participants[{{ $index - 1 }}][ticket_id]" value="{{ $ticket->id }}">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                            Nome Completo *
                                            <x-tooltip text="Informe o nome completo do participante conforme documento de identidade. Este nome será exibido no ingresso." />
                                        </label>
                                        <input type="text"
                                               name="participants[{{ $index - 1 }}][name]"
                                               value="{{ old("participants.{$loop->index}.name", $ticket->participant?->name !== 'Pending' ? $ticket->participant?->name : '') }}"
                                               required
                                               placeholder="João da Silva"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                            E-mail *
                                            <x-tooltip text="O ingresso será enviado para este e-mail após a confirmação do pagamento." />
                                        </label>
                                        <input type="email"
                                               name="participants[{{ $index - 1 }}][email]"
                                               value="{{ old("participants.{$loop->index}.email", $ticket->participant?->email !== 'pending@pending.com' ? $ticket->participant?->email : '') }}"
                                               required
                                               placeholder="email@exemplo.com"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                            Telefone / WhatsApp
                                            <x-tooltip text="Número de contato para comunicações sobre o evento. Formato: (00) 00000-0000" />
                                        </label>
                                        <input type="tel"
                                               name="participants[{{ $index - 1 }}][phone]"
                                               value="{{ old("participants.{$loop->index}.phone", $ticket->participant?->phone) }}"
                                               placeholder="(11) 99999-9999"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mask-phone">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                            CPF
                                            <x-tooltip text="Informe o CPF do participante. Pode ser solicitado na entrada do evento para verificação de identidade." />
                                        </label>
                                        <input type="text"
                                               name="participants[{{ $index - 1 }}][document]"
                                               value="{{ old("participants.{$loop->index}.document", $ticket->participant?->document) }}"
                                               placeholder="000.000.000-00"
                                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mask-cpf">
                                    </div>
                                </div>

                                {{-- Custom Fields --}}
                                @if($order->event->customFields->count())
                                    <div class="mt-4 space-y-3 border-t pt-4">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Informações adicionais do evento</p>
                                        @foreach($order->event->customFields as $field)
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    {{ $field->label }}{{ $field->required ? ' *' : '' }}
                                                </label>
                                                <input type="text"
                                                       name="participants[{{ $index - 1 }}][custom_fields][{{ $field->id }}]"
                                                       {{ $field->required ? 'required' : '' }}
                                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endforeach

                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 font-semibold text-lg transition">
                        Continuar para Pagamento →
                    </button>
                </form>
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border p-6 sticky top-4">
                    <h2 class="font-semibold text-gray-900 text-lg mb-4">Resumo do Pedido</h2>

                    <div class="text-sm text-gray-600 mb-4">
                        <p class="font-medium text-gray-800">{{ $order->event->title }}</p>
                        <p class="mt-1">{{ $order->event->start_date->format('d/m/Y \à\s H:i') }}</p>
                        <p>{{ $order->event->location }}</p>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="py-2 flex justify-between text-sm">
                                <span class="text-gray-700">{{ $item->ticketType->name }} × {{ $item->quantity }}</span>
                                <span class="font-medium">R$ {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-3 mt-3 flex justify-between font-semibold">
                        <span>Total</span>
                        <span class="text-indigo-600">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Phone mask
        document.querySelectorAll('.mask-phone').forEach(function(el) {
            IMask(el, {
                mask: [
                    { mask: '(00) 0000-0000' },
                    { mask: '(00) 00000-0000' }
                ],
                dispatch: function(appended, dynamicMasked) {
                    var number = (dynamicMasked.value + appended).replace(/\D/g, '');
                    return number.length > 10 ? dynamicMasked.compiledMasks[1] : dynamicMasked.compiledMasks[0];
                }
            });
        });

        // CPF mask
        document.querySelectorAll('.mask-cpf').forEach(function(el) {
            IMask(el, { mask: '000.000.000-00' });
        });
    });
    </script>
    @endpush
</x-layouts.checkout>
