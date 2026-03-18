<x-mail::message>
# Você foi adicionado à equipe!

Você foi adicionado à equipe de check-in do evento **{{ $event->title }}**.

📅 {{ $event->start_date->format('d/m/Y \à\s H:i') }}
@if($event->location)
📍 {{ $event->location }}
@endif

<x-mail::button :url="route('staff.checkin', $event)">
Acessar check-in
</x-mail::button>

Obrigado,
{{ config('app.name') }}
</x-mail::message>
