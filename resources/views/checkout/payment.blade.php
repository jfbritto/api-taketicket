<x-layouts.checkout :title="'Pagamento — ' . $order->event->title">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Pagamento</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Payment Section --}}
            <div class="lg:col-span-2">

                @if(isset($pixData) && $pixData)
                    {{-- PIX QR Code Display --}}
                    <div class="bg-white rounded-lg shadow-sm border p-6"
                         x-data="pixPoller('{{ route('checkout.status', $order) }}')"
                         x-init="startPolling()">

                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pagar via PIX</h2>

                        <div class="text-center mb-6">
                            @if(!empty($pixData['encodedImage']))
                                <img src="data:image/png;base64,{{ $pixData['encodedImage'] }}"
                                     alt="PIX QR Code"
                                     class="mx-auto w-48 h-48 border rounded-lg">
                            @endif
                        </div>

                        @if(!empty($pixData['payload']))
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Código PIX — Copia e Cola</label>
                                <div class="flex gap-2">
                                    <input type="text"
                                           readonly
                                           value="{{ $pixData['payload'] }}"
                                           class="flex-1 border-gray-300 rounded-lg bg-gray-50 text-sm font-mono"
                                           id="pix-payload">
                                    <button type="button"
                                            @click="copyPix()"
                                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm whitespace-nowrap">
                                        <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <p class="text-sm text-gray-500 mt-4 text-center">
                            Abra o app do seu banco, escolha pagar via PIX e escaneie o QR Code ou cole o código acima.
                        </p>

                        <div class="mt-4 text-center text-sm text-gray-500" x-show="!paid">
                            Aguardando confirmação do pagamento...
                        </div>
                        <div class="mt-4 text-center text-green-600 font-semibold" x-show="paid">
                            Pagamento confirmado! Redirecionando...
                        </div>
                    </div>

                @else
                    {{-- Payment Method Selection --}}
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Selecione a Forma de Pagamento</h2>

                        <form method="POST" action="{{ route('checkout.processPayment', $order) }}" x-data="{ method: 'PIX' }">
                            @csrf

                            <div class="space-y-3 mb-6">
                                <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer"
                                       :class="method === 'PIX' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                                    <input type="radio" name="billing_type" value="PIX" x-model="method" class="text-indigo-600">
                                    <div>
                                        <p class="font-medium">PIX</p>
                                        <p class="text-sm text-gray-500">Pagamento instantâneo, aprovado na hora</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer"
                                       :class="method === 'CREDIT_CARD' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                                    <input type="radio" name="billing_type" value="CREDIT_CARD" x-model="method" class="text-indigo-600">
                                    <div>
                                        <p class="font-medium">Cartão de Crédito</p>
                                        <p class="text-sm text-gray-500">Pague com segurança via nossa parceira de pagamento</p>
                                    </div>
                                </label>
                            </div>

                            <button type="submit"
                                    class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 font-semibold text-lg transition">
                                Prosseguir com o Pagamento
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Order Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border p-6 sticky top-4">
                    <h2 class="font-semibold text-gray-900 text-lg mb-4">Resumo do Pedido</h2>

                    <div class="text-sm text-gray-600 mb-4">
                        <p class="font-medium text-gray-800">{{ $order->event->title }}</p>
                        <p class="mt-1">{{ $order->event->start_date->format('d M Y, H:i') }}</p>
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
    function pixPoller(statusUrl) {
        return {
            paid: false,
            copied: false,
            intervalId: null,
            startPolling() {
                this.intervalId = setInterval(async () => {
                    try {
                        const res = await fetch(statusUrl, {
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        const data = await res.json();
                        if (data.status === 'paid') {
                            this.paid = true;
                            clearInterval(this.intervalId);
                            setTimeout(() => {
                                window.location.href = '{{ route('checkout.success') }}?order={{ $order->id }}';
                            }, 1500);
                        }
                    } catch (e) {
                        // silently ignore network errors
                    }
                }, 5000);
            },
            copyPix() {
                const input = document.getElementById('pix-payload');
                if (input) {
                    navigator.clipboard.writeText(input.value).then(() => {
                        this.copied = true;
                        setTimeout(() => { this.copied = false; }, 2000);
                    });
                }
            }
        };
    }
    </script>
    @endpush
</x-layouts.checkout>
