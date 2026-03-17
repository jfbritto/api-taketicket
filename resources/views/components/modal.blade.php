@props(['name', 'title' => null, 'maxWidth' => 'md'])

@php
$maxWidthClass = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
][$maxWidth] ?? 'max-w-md';
@endphp

<div x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
     x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>
    <div x-show="open" x-transition class="relative bg-white rounded-lg shadow-xl {{ $maxWidthClass }} w-full mx-4 p-6">
        @if($title)
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h3>
        @endif
        {{ $slot }}
    </div>
</div>
