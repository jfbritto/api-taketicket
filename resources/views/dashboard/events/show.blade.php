<x-layouts.dashboard :header="$event->title">
    <div class="space-y-5">

        {{-- Header card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
            <div class="flex items-center justify-between">
                {{-- Left: back + info --}}
                <div class="flex items-center gap-4 min-w-0">
                    <a href="{{ route('dashboard.events') }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-lg font-bold text-gray-900 truncate">{{ $event->title }}</h1>
                            {{-- Status badge --}}
                            @if($event->status->value === 'published')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold flex-shrink-0"
                                      style="background-color:#dcfce7;color:#16a34a;">Publicado</span>
                            @elseif($event->status->value === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold flex-shrink-0"
                                      style="background-color:#fef9c3;color:#a16207;">Rascunho</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold flex-shrink-0"
                                      style="background-color:#f3f4f6;color:#6b7280;">Cancelado</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-0.5">
                            @if($event->city || $event->state)
                                {{ $event->city }}{{ $event->city && $event->state ? ', ' : '' }}{{ $event->state }} ·
                            @endif
                            {{ $event->start_date->format('d/m/Y \à\s H:i') }}
                            @if($event->location) · {{ $event->location }} @endif
                        </p>
                    </div>
                </div>

                {{-- Right: action buttons --}}
                <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                    <a href="{{ route('dashboard.events.edit', $event) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>

                    @if($event->status->value === 'draft')
                        <form method="POST" action="{{ route('dashboard.events.publish', $event) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition"
                                    style="background-color:#16a34a;color:white;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Publicar
                            </button>
                        </form>
                    @endif

                    @if($event->status->value !== 'cancelled')
                        <form id="form-cancel-{{ $event->id }}" method="POST" action="{{ route('dashboard.events.cancel', $event) }}">
                            @csrf
                            @method('PATCH')
                            <button type="button" onclick="confirmCancelEvent()"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium border transition"
                                    style="color:#ef4444;border-color:#fecaca;background:white;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar evento
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats row: 4 cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Ingressos Vendidos --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ingressos Vendidos</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $totalSold }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">de {{ $totalCapacity }} disponíveis</p>
                </div>
            </div>

            {{-- Receita Líquida --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Receita Líquida</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">valor repassado ao organizador</p>
                </div>
            </div>

            {{-- Check-ins --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Check-ins</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $totalCheckins }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">de {{ $totalSold }} vendidos</p>
                </div>
            </div>

            {{-- Disponíveis --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Disponíveis</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $totalCapacity - $totalSold }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">ingressos restantes</p>
                </div>
            </div>
        </div>

        {{-- Two column bottom --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Ticket types (col-span-2) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Tipos de Ingresso</h2>
                    <a href="{{ route('dashboard.events.edit', $event) }}"
                       class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition">
                        Gerenciar →
                    </a>
                </div>

                @if($ticketTypes->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <p class="text-gray-500 text-sm">Nenhum tipo de ingresso cadastrado.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-left text-gray-500 border-b border-gray-100 bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-3 font-medium">Nome</th>
                                    <th class="px-6 py-3 font-medium">Preço</th>
                                    <th class="px-6 py-3 font-medium">Vendidos</th>
                                    <th class="px-6 py-3 font-medium">Capacidade</th>
                                    <th class="px-6 py-3 font-medium">Disponíveis</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($ticketTypes as $type)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-medium text-gray-900">{{ $type->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            R$ {{ number_format($type->price, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-gray-900">{{ $type->sold_count }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ $type->quantity }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                                {{ $type->available > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $type->available }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Recent orders (col-span-1) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Últimos Pedidos</h2>
                    <a href="{{ route('dashboard.orders', $event) }}"
                       class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition">
                        Ver todos →
                    </a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-500 text-sm">Nenhum pedido ainda.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($recentOrders as $order)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-indigo-600">
                                            {{ strtoupper(substr($order->user?->name ?? 'A', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $order->user?->name ?? 'Anônimo' }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-2">
                                    <span class="text-sm font-semibold text-gray-900">
                                        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/50">
                        <a href="{{ route('dashboard.orders', $event) }}"
                           class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition">
                            Ver todos os pedidos →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Equipe de Check-in --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-800">Equipe de Check-in</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Colaboradores autorizados a realizar check-in neste evento.</p>
                </div>
            </div>

            {{-- Invite form --}}
            <div class="px-6 py-4 border-b border-gray-100">
                @if(session('success'))
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;margin-bottom:12px;">
                        <p style="font-size:13px;color:#16a34a;margin:0;font-weight:500;">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('dashboard.collaborators.store', $event) }}" style="display:flex;gap:8px;align-items:flex-start;">
                    @csrf
                    <div style="flex:1;">
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="email@colaborador.com"
                               style="width:100%;box-sizing:border-box;padding:10px 14px;border:1.5px solid {{ $errors->has('email') ? '#fca5a5' : '#e5e7eb' }};border-radius:8px;font-size:14px;font-family:inherit;outline:none;">
                        @error('email')
                            <p style="font-size:12px;color:#dc2626;margin:4px 0 0 0;">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            style="padding:10px 20px;background:#4f46e5;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit;">
                        Convidar
                    </button>
                </form>
            </div>

            {{-- Collaborator list --}}
            @if($collaborators->isEmpty())
                <div style="text-align:center;padding:40px;">
                    <p style="color:#9ca3af;font-size:14px;margin:0;">Nenhum colaborador adicionado ainda.</p>
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($collaborators as $collaborator)
                        @php
                            $displayName = $collaborator->user?->name ?? $collaborator->invitee_email;
                            $isRevoked = $collaborator->status === 'revoked';
                            $isExpired = $collaborator->status === 'active' && $collaborator->isExpired();
                            $isActive = $collaborator->status === 'active' && ! $collaborator->isExpired();
                            $isPending = $collaborator->status === 'pending';
                            $showButton = $isActive || $isPending;
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 24px;">
                            <div>
                                <p style="font-size:14px;font-weight:600;color:#111827;margin:0;">{{ $displayName }}</p>
                                @if($collaborator->user && $collaborator->user->email !== $collaborator->invitee_email)
                                    <p style="font-size:12px;color:#9ca3af;margin:2px 0 0 0;">{{ $collaborator->invitee_email }}</p>
                                @endif
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($isActive)
                                    <span style="background:#dcfce7;color:#16a34a;font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">Ativo</span>
                                @elseif($isPending)
                                    <span style="background:#f3f4f6;color:#6b7280;font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">Aguardando cadastro</span>
                                @elseif($isExpired)
                                    <span style="background:#f3f4f6;color:#9ca3af;font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">Expirado</span>
                                @elseif($isRevoked)
                                    <span style="background:#f3f4f6;color:#d1d5db;font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">Revogado</span>
                                @endif

                                @if($showButton)
                                    <form method="POST" action="{{ route('dashboard.collaborators.destroy', [$event, $collaborator]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="font-size:12px;color:#dc2626;background:none;border:1px solid #fecaca;border-radius:6px;padding:4px 10px;cursor:pointer;font-family:inherit;"
                                                onclick="return confirm('Remover acesso de {{ $displayName }}?')">
                                            Remover
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
    <script>
    function confirmCancelEvent() {
        Swal.fire({
            title: 'Cancelar evento?',
            text: 'Esta ação não pode ser desfeita. O evento será cancelado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Voltar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-cancel-{{ $event->id }}').submit();
            }
        });
    }
    </script>
    @endpush
</x-layouts.dashboard>
