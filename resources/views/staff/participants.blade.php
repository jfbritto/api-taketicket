<x-layouts.staff :event="$event">
    <div style="margin-bottom:24px;">
        <form method="GET" style="display:flex;gap:8px;">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Buscar por nome ou código..."
                   style="flex:1;padding:10px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit;outline:none;">
            <button type="submit" style="padding:10px 20px;background:#4f46e5;color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;">Buscar</button>
            @if(request('q'))
                <a href="{{ route('staff.participants', $event) }}" style="padding:10px 16px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;">Limpar</a>
            @endif
        </form>
    </div>

    <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;">
            <p style="margin:0;font-size:14px;color:#6b7280;">{{ $participants->total() }} participante{{ $participants->total() !== 1 ? 's' : '' }}</p>
        </div>

        @if($participants->isEmpty())
            <div style="text-align:center;padding:48px;">
                <p style="color:#9ca3af;margin:0;">Nenhum participante encontrado.</p>
            </div>
        @else
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid #f3f4f6;background:#f9fafb;">
                        <th style="padding:10px 20px;font-weight:600;color:#6b7280;">Nome</th>
                        <th style="padding:10px 20px;font-weight:600;color:#6b7280;">Tipo de ingresso</th>
                        <th style="padding:10px 20px;font-weight:600;color:#6b7280;">Código</th>
                        <th style="padding:10px 20px;font-weight:600;color:#6b7280;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $participant)
                        <tr style="border-bottom:1px solid #f9fafb;">
                            <td style="padding:12px 20px;color:#111827;font-weight:500;">{{ $participant->name }}</td>
                            <td style="padding:12px 20px;color:#6b7280;">{{ $participant->ticket->ticketType->name ?? '—' }}</td>
                            <td style="padding:12px 20px;color:#6b7280;font-family:monospace;">{{ $participant->ticket->ticket_code }}</td>
                            <td style="padding:12px 20px;">
                                @if($participant->ticket->status->value === 'used')
                                    <span style="background:#dcfce7;color:#16a34a;font-size:12px;font-weight:600;padding:3px 8px;border-radius:6px;">Check-in feito</span>
                                @else
                                    <span style="background:#f3f4f6;color:#6b7280;font-size:12px;font-weight:600;padding:3px 8px;border-radius:6px;">Válido</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($participants->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #f3f4f6;">
                    {{ $participants->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.staff>
