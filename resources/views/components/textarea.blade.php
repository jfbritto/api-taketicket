@props(['label' => null, 'name', 'value' => null, 'required' => false, 'rows' => 4])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}"
              {{ $required ? 'required' : '' }}
              {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
