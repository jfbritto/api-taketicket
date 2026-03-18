<x-layouts.dashboard header="Financeiro">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 4px;">Financeiro</h2>
            <p style="font-size:13.5px;color:#64748b;margin:0;">Acompanhe seus recebimentos e o histórico de pedidos pagos.</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 22px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="font-size:11.5px;font-weight:600;color:#94a3b8;margin:0 0 5px;text-transform:uppercase;letter-spacing:0.5px;">Valor Líquido</p>
            <p style="font-size:22px;font-weight:800;color:#16a34a;margin:0 0 4px;">R$ {{ number_format($totalNet, 2, ',', '.') }}</p>
            <p style="font-size:11.5px;color:#94a3b8;margin:0;">Após taxas da plataforma</p>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 22px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <p style="font-size:11.5px;font-weight:600;color:#94a3b8;margin:0 0 5px;text-transform:uppercase;letter-spacing:0.5px;">Total Bruto</p>
            <p style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px;">R$ {{ number_format($totalGross, 2, ',', '.') }}</p>
            <p style="font-size:11.5px;color:#94a3b8;margin:0;">Valor total arrecadado</p>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 22px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="font-size:11.5px;font-weight:600;color:#94a3b8;margin:0 0 5px;text-transform:uppercase;letter-spacing:0.5px;">Taxa Plataforma</p>
            <p style="font-size:22px;font-weight:800;color:#dc2626;margin:0 0 4px;">R$ {{ number_format($totalFee, 2, ',', '.') }}</p>
            <p style="font-size:11.5px;color:#94a3b8;margin:0;">Total retido pela TakeTicket</p>
        </div>

        <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;padding:20px 22px;">
            <div style="width:40px;height:40px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="18" height="18" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p style="font-size:11.5px;font-weight:600;color:#94a3b8;margin:0 0 5px;text-transform:uppercase;letter-spacing:0.5px;">Pedidos Pagos</p>
            <p style="font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px;">{{ $totalPaidCount }}</p>
            <p style="font-size:11.5px;color:#94a3b8;margin:0;">Transações confirmadas</p>
        </div>

    </div>

    {{-- Table --}}
    <div style="background:white;border-radius:16px;border:1px solid #f1f5f9;overflow:hidden;">
        <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 3px;">Histórico de Recebimentos</p>
                <p style="font-size:12.5px;color:#94a3b8;margin:0;">{{ $recentOrders->total() }} pedido{{ $recentOrders->total() !== 1 ? 's' : '' }} pagos no total</p>
            </div>
        </div>

        @if($recentOrders->isEmpty())
            <div style="text-align:center;padding:64px 24px;">
                <div style="width:56px;height:56px;border-radius:16px;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <svg width="28" height="28" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p style="font-size:15px;font-weight:700;color:#1e293b;margin:0 0 6px;">Nenhum pagamento recebido ainda</p>
                <p style="font-size:13px;color:#94a3b8;margin:0;">Os recebimentos aparecerão aqui quando houver vendas confirmadas.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #f1f5f9;">
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Pedido</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Comprador</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Evento</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Bruto</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Taxa</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Líquido</th>
                            <th style="padding:11px 20px;text-align:left;font-size:11.5px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
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
                                        <div>
                                            <p style="font-weight:600;color:#334155;margin:0 0 1px;font-size:13px;">{{ $order->user->name }}</p>
                                            <p style="font-size:11.5px;color:#94a3b8;margin:0;">{{ $order->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:14px 20px;max-width:160px;">
                                    <p style="font-size:13px;color:#475569;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $order->event->title }}</p>
                                </td>
                                <td style="padding:14px 20px;font-size:13px;color:#475569;">
                                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                </td>
                                <td style="padding:14px 20px;font-size:13px;color:#dc2626;font-weight:500;">
                                    - R$ {{ number_format($order->platform_fee, 2, ',', '.') }}
                                </td>
                                <td style="padding:14px 20px;">
                                    <span style="font-size:14px;font-weight:700;color:#16a34a;">R$ {{ number_format($order->organizer_amount, 2, ',', '.') }}</span>
                                </td>
                                <td style="padding:14px 20px;font-size:12.5px;color:#94a3b8;">
                                    {{ $order->updated_at->format('d/m/Y') }}<br>
                                    <span style="font-size:11.5px;">{{ $order->updated_at->format('H:i') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($recentOrders->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
                    {{ $recentOrders->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>

</x-layouts.dashboard>
