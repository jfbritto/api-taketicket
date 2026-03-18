<x-layouts.staff>
    <div style="margin-bottom:32px;">
        <h1 style="font-size:24px;font-weight:800;color:#111827;margin:0 0 8px 0;">Seus eventos</h1>
        <p style="font-size:15px;color:#6b7280;margin:0;">Selecione o evento para acessar o check-in.</p>
    </div>

    @if($collaborations->isEmpty())
        <div style="text-align:center;padding:64px 24px;background:white;border-radius:16px;border:1px solid #f3f4f6;">
            <p style="font-size:16px;color:#374151;font-weight:600;margin:0 0 8px 0;">Nenhum evento ativo no momento</p>
            <p style="font-size:14px;color:#9ca3af;margin:0;">Você não tem acesso ativo a nenhum evento no momento.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
            @foreach($collaborations as $collaboration)
                <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;padding:24px;">
                    <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0 0 8px 0;">{{ $collaboration->event->title }}</h2>
                    <p style="font-size:13px;color:#9ca3af;margin:0 0 4px 0;">{{ $collaboration->event->start_date->format('d/m/Y \à\s H:i') }}</p>
                    @if($collaboration->event->location)
                        <p style="font-size:13px;color:#9ca3af;margin:0 0 20px 0;">📍 {{ $collaboration->event->location }}</p>
                    @else
                        <div style="margin-bottom:20px;"></div>
                    @endif
                    <a href="{{ route('staff.checkin', $collaboration->event) }}"
                       style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;text-decoration:none;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;">
                        Acessar Check-in →
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.staff>
