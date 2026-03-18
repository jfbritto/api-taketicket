@props(['title' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-100 ' . ($padding ? 'p-6' : '')]) }}>
    @if($title)
        <h3 class="text-sm font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>
