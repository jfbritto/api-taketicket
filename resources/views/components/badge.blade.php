@props(['type' => 'default'])

@php
$colors = [
    'draft' => 'bg-gray-100 text-gray-700',
    'published' => 'bg-green-100 text-green-700',
    'cancelled' => 'bg-red-100 text-red-700',
    'finished' => 'bg-blue-100 text-blue-700',
    'valid' => 'bg-green-100 text-green-700',
    'used' => 'bg-blue-100 text-blue-700',
    'paid' => 'bg-green-100 text-green-700',
    'pending' => 'bg-yellow-100 text-yellow-700',
    'awaiting_payment' => 'bg-yellow-100 text-yellow-700',
    'expired' => 'bg-gray-100 text-gray-700',
    'default' => 'bg-gray-100 text-gray-700',
];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$type] ?? $colors['default'] }}">
    {{ $slot }}
</span>
