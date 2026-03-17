@props(['label' => null, 'name', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => 'Select...'])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif
    <select name="{{ $name }}" id="{{ $name }}" {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 border']) }}>
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $key => $optionLabel)
            <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
