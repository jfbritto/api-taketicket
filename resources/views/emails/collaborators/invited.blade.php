<x-mail::message>
# Você foi convidado!

**{{ $collaborator->inviter->name }}** convidou você para fazer check-in no evento **{{ $collaborator->event->title }}**.

📅 {{ $collaborator->event->start_date->format('d/m/Y \à\s H:i') }}
@if($collaborator->event->location)
📍 {{ $collaborator->event->location }}
@endif

<x-mail::button :url="$signedUrl">
Aceitar convite
</x-mail::button>

Este convite expira em 7 dias.

Obrigado,
{{ config('app.name') }}
</x-mail::message>
