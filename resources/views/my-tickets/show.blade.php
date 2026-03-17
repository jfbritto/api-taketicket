<x-layouts.app title="Ingresso — {{ $ticket->ticket_code }}">
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var qr = qrcode(0, 'M');
            qr.addData('{{ $ticket->qr_code_payload }}');
            qr.make();
            document.getElementById('qr-code').innerHTML = qr.createImgTag(5, 8);
        });
    </script>
    @endpush

    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('my-tickets') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Voltar para Meus Ingressos</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            {{-- Header --}}
            <div class="bg-indigo-600 px-6 py-5">
                <h1 class="text-xl font-bold text-white">{{ $ticket->event->title }}</h1>
                <p class="text-indigo-200 text-sm mt-1">{{ $ticket->event->start_date->format('d M Y, H:i') }}</p>
                <p class="text-indigo-200 text-sm">{{ $ticket->event->location }}</p>
            </div>

            {{-- Status Badge --}}
            <div class="px-6 pt-5 pb-2 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500">Situação</span>
                @if($ticket->status === \App\Enums\TicketStatus::VALID)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">Válido</span>
                @elseif($ticket->status === \App\Enums\TicketStatus::USED)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">Utilizado</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">Cancelado</span>
                @endif
            </div>

            <div class="px-6 py-4 space-y-4">
                {{-- Ticket Type --}}
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 font-medium">Tipo de Ingresso</span>
                    <span class="text-gray-800">{{ $ticket->ticketType->name }}</span>
                </div>

                {{-- Ticket Code --}}
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 font-medium">Código do Ingresso</span>
                    <span class="text-gray-800 font-mono">{{ $ticket->ticket_code }}</span>
                </div>

                @if($ticket->participant)
                <div class="border-t pt-4">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3">Dados do Participante</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nome</span>
                            <span class="text-gray-800">{{ $ticket->participant->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">E-mail</span>
                            <span class="text-gray-800">{{ $ticket->participant->email }}</span>
                        </div>
                        @if($ticket->participant->phone)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Telefone</span>
                            <span class="text-gray-800">{{ $ticket->participant->phone }}</span>
                        </div>
                        @endif
                        @if($ticket->participant->document)
                        <div class="flex justify-between">
                            <span class="text-gray-500">CPF</span>
                            <span class="text-gray-800">{{ $ticket->participant->document }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- QR Code --}}
                <div class="border-t pt-4 flex flex-col items-center">
                    <p class="text-sm text-gray-500 mb-3">Apresente este QR Code na entrada do evento</p>
                    <div id="qr-code" class="flex justify-center"></div>
                    <p class="mt-3 text-xs text-gray-400 font-mono">{{ $ticket->qr_code_payload }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
