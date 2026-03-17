@props(['type' => 'info', 'message'])

@php
$colors = [
    'success' => 'bg-green-100 text-green-800 border-green-300',
    'error' => 'bg-red-100 text-red-800 border-red-300',
    'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
    'info' => 'bg-blue-100 text-blue-800 border-blue-300',
];
@endphp

<div class="border rounded-lg px-4 py-3 mx-4 mt-4 {{ $colors[$type] ?? $colors['info'] }}" x-data="{ show: true }" x-show="show">
    <div class="flex justify-between items-center">
        <span>{{ $message }}</span>
        <button @click="show = false" class="ml-4">&times;</button>
    </div>
</div>
