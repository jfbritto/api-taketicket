<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso ao Check-in — TakeTicket</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                {{-- Header --}}
                <tr>
                    <td align="center" style="padding:0 0 24px 0;">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:14px;width:48px;height:48px;text-align:center;vertical-align:middle;">
                                    <span style="display:inline-block;font-size:22px;line-height:48px;">🎟️</span>
                                </td>
                                <td style="padding-left:12px;vertical-align:middle;">
                                    <span style="font-size:22px;font-weight:800;color:#4f46e5;letter-spacing:-0.5px;">TakeTicket</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Card --}}
                <tr>
                    <td style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                        {{-- Card top accent --}}
                        <tr>
                            <td style="background:linear-gradient(135deg,#4f46e5,#7c3aed);height:6px;"></td>
                        </tr>

                        {{-- Body --}}
                        <tr>
                            <td style="padding:40px 40px 32px 40px;">

                                {{-- Icon badge --}}
                                <div style="width:56px;height:56px;background:#dcfce7;border-radius:16px;margin-bottom:24px;">
                                    <span style="font-size:28px;line-height:56px;display:block;text-align:center;">🚀</span>
                                </div>

                                <h1 style="margin:0 0 8px 0;font-size:24px;font-weight:800;color:#111827;letter-spacing:-0.5px;">
                                    Acesso liberado!
                                </h1>
                                <p style="margin:0 0 28px 0;font-size:15px;color:#6b7280;line-height:1.6;">
                                    Você foi adicionado à equipe de check-in do evento abaixo. Seu acesso já está ativo.
                                </p>

                                {{-- Event card --}}
                                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f7ff;border:1.5px solid #e0e7ff;border-radius:14px;margin-bottom:28px;">
                                    <tr>
                                        <td style="padding:20px 24px;">
                                            <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;color:#8b5cf6;text-transform:uppercase;letter-spacing:0.8px;">Evento</p>
                                            <p style="margin:0 0 16px 0;font-size:18px;font-weight:800;color:#111827;letter-spacing:-0.3px;">{{ $event->title }}</p>
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="padding-right:20px;font-size:13px;color:#6b7280;">
                                                        📅 {{ $event->start_date->format('d/m/Y \à\s H:i') }}
                                                    </td>
                                                    @if($event->location)
                                                    <td style="font-size:13px;color:#6b7280;">
                                                        📍 {{ $event->location }}
                                                    </td>
                                                    @endif
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                {{-- CTA --}}
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            <a href="{{ route('staff.checkin', $event) }}"
                                               style="display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;border-radius:12px;padding:14px 36px;letter-spacing:-0.2px;box-shadow:0 4px 16px rgba(79,70,229,0.4);">
                                                Acessar check-in →
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin:24px 0 0 0;font-size:13px;color:#9ca3af;text-align:center;">
                                    Seu acesso é válido até o encerramento do evento.
                                </p>
                            </td>
                        </tr>

                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td align="center" style="padding:24px 0 0 0;">
                        <p style="margin:0;font-size:12px;color:#9ca3af;">
                            © {{ date('Y') }} TakeTicket. Todos os direitos reservados.
                        </p>
                        <p style="margin:6px 0 0 0;font-size:12px;color:#d1d5db;">
                            Você recebeu este e-mail porque foi adicionado diretamente por um organizador.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
