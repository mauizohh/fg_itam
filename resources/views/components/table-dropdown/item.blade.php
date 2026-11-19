@props([
    'label' => null,
    'icon' => null,
    'danger' => false,
    'href' => null,
])

@if ($href)
    <a href="{{ $href }}"
        {{ $attributes->merge([
            'class' => 'w-full px-3 py-2 text-xs flex items-center cursor-pointer rounded-md transition-colors duration-150 text-left ' . 
                       ($danger 
                           ? 'text-red-600 hover:text-red-700 hover:bg-red-50' 
                           : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100')
        ]) }}>
        @if($icon)
            <x-icon :name="$icon" @class([
                'w-4 h-4 mr-2 shrink-0',
                'text-red-500' => $danger,
                'text-gray-500' => !$danger,
            ]) />
        @endif
        <span>{{ $label ?? $slot }}</span>
    </a>
@else
    <button type="button"
        {{ $attributes->merge([
            'class' => 'w-full px-3 py-2 text-xs flex items-center cursor-pointer rounded-md transition-colors duration-150 text-left ' . 
                       ($danger 
                           ? 'text-red-600 hover:text-red-700 hover:bg-red-50' 
                           : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100')
        ]) }}>
        @if($icon)
            <x-icon :name="$icon" @class([
                'w-4 h-4 mr-2 shrink-0',
                'text-red-500' => $danger,
                'text-gray-500' => !$danger,
            ]) />
        @endif
        <span>{{ $label ?? $slot }}</span>
    </button>
@endif
