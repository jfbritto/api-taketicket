<x-layouts.dashboard header="Dashboard">

    {{-- Welcome --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 4px;">Olá, {{ auth()->user()->name }}! 👋</h2>
            <p style="font-size:13.5px;color:#64748b;margin:0;">Aqui está um resumo dos seus eventos e vendas.</p>
        </div>
        <a href="{{ url('/dashboard/events/create') }}"
           style="display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Criar Evento
        </a>
    </div>

    {{-- Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;">

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 22px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="18" height="18" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p style="font-size:11.5px;font-weight:600;color:#94a3b8;margin:0 0 5px;text-transform:uppercase;letter-spacing:0.5px;">Total de Eventos</p>
            <p style="font-size:28px;font-weight:800;color:#0f172a;margin:0;">{{ $totalEvents }}</p>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 22px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="font-size:11.5px;font-weight:600;color:#94a3b8;margin:0 0 5px;text-transform:uppercase;letter-spacing:0.5px;">Total em Vendas</p>
            <p style="font-size:28px;font-weight:800;color:#16a34a;margin:0;">R$ {{ number_format($totalSales, 2, ',', '.') }}</p>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 22px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p style="font-size:11.5px;font-weight:600;color:#94a3b8;margin:0 0 5px;text-transform:uppercase;letter-spacing:0.5px;">Total de Participantes</p>
            <p style="font-size:28px;font-weight:800;color:#0f172a;margin:0;">{{ $totalParticipants }}</p>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 22px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#fef9c3;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="18" height="18" fill="none" stroke="#ca8a04" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="font-size:11.5px;font-weight:600;color:#94a3b8;margin:0 0 5px;text-transform:uppercase;letter-spacing:0.5px;">Taxa de Check-in</p>
            <p style="font-size:28px;font-weight:800;color:#ca8a04;margin:0;">{{ $checkinRate }}%</p>
        </div>

    </div>

    {{-- Recent Orders --}}
    <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;">
        <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 3px;">Pedidos Recentes</p>
                <p style="font-size:12.5px;color:#94a3b8;margin:0;">Últimas transações de todos os seus eventos</p>
            </div>
            <a href="{{ url('/dashboard/financeiro') }}"
               style="font-size:13px;color:#4f46e5;font-weight:600;text-decoration:none;">
                Ver todos →
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div style="text-align:center;padding:64px 24px;">
                <div style="width:48px;height:48px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="22" height="22" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p style="font-size:14px;font-weight:600;color:#475569;margin:0 0 4px;">Nenhum pedido ainda.</p>
                <p style="font-size:13px;color:#94a3b8;margin:0;">Os pedidos aparecerão aqui quando você tiver vendas.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Pedido</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Comprador</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Evento</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Valor</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Status</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusLabels = ['pending' => 'Pendente', 'awaiting_payment' => 'Aguardando', 'paid' => 'Pago', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado', 'expired' => 'Expirado'];
                            $avatarColors = [
                                'linear-gradient(135deg,#4f46e5,#7c3aed)',
                                'linear-gradient(135deg,#059669,#0d9488)',
                                'linear-gradient(135deg,#ea580c,#db2777)',
                                'linear-gradient(135deg,#0284c7,#4f46e5)',
                                'linear-gradient(135deg,#dc2626,#ea580c)',
                                'linear-gradient(135deg,#7c3aed,#6d28d9)',
                                'linear-gradient(135deg,#0891b2,#0284c7)',
                                'linear-gradient(135deg,#ca8a04,#d97706)',
                            ];
                        @endphp
                        @foreach($recentOrders as $order)
                            @php
                                $avatarGrad = $avatarColors[$order->user->id % 8];
                                $initials = strtoupper(substr($order->user->name, 0, 1)) . strtoupper(substr(strstr($order->user->name, ' ') ?: ' x', 1, 1));
                            @endphp
                            <tr style="border-bottom:1px solid #f8fafc;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                                <td style="padding:14px 20px;">
                                    <span style="font-weight:700;color:#0f172a;font-size:13px;">#{{ $order->id }}</span>
                                </td>
                                <td style="padding:14px 20px;">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div style="width:34px;height:34px;border-radius:50%;background:{{ $avatarGrad }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0;">
                                            {{ $initials }}
                                        </div>
                                        <span style="font-weight:600;color:#334155;font-size:13px;">{{ $order->user->name }}</span>
                                    </div>
                                </td>
                                <td style="padding:14px 20px;max-width:180px;">
                                    <p style="font-size:13px;color:#475569;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $order->event->title }}</p>
                                </td>
                                <td style="padding:14px 20px;">
                                    <span style="font-size:14px;font-weight:700;color:#0f172a;">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                                </td>
                                <td style="padding:14px 20px;">
                                    <x-badge :type="$order->status->value">{{ $statusLabels[$order->status->value] ?? $order->status->value }}</x-badge>
                                </td>
                                <td style="padding:14px 20px;font-size:12.5px;color:#94a3b8;">
                                    {{ $order->created_at->format('d/m/Y') }}<br>
                                    <span style="font-size:11.5px;">{{ $order->created_at->format('H:i') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</x-layouts.dashboard>
