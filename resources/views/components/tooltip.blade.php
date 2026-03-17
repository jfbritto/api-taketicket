@props(['text'])

<span class="relative inline-flex items-center group ml-1" x-data="{ open: false }">
    <button type="button"
            @mouseenter="open = true"
            @mouseleave="open = false"
            @focus="open = true"
            @blur="open = false"
            class="w-4 h-4 rounded-full bg-gray-300 text-gray-600 text-xs font-bold flex items-center justify-center hover:bg-gray-400 cursor-help select-none"
            tabindex="-1">?</button>
    <span x-show="open"
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-100"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-60 bg-gray-800 text-white text-xs rounded-lg p-2.5 z-20 pointer-events-none shadow-lg"
          style="display: none;">
        {{ $text }}
        <span class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></span>
    </span>
</span>
