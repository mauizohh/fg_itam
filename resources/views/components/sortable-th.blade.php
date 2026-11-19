@props([
    'field',
    'currentField' => '',
    'currentDirection' => 'asc',
    'align' => 'left',
])

@php
    $isActive = $currentField === $field;
    $direction = strtolower((string) $currentDirection) === 'desc' ? 'desc' : 'asc';
    $ariaSort = $isActive ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none';
    $alignmentClasses = match($align) {
        'center' => 'justify-center text-center',
        'right' => 'justify-end text-right',
        default => 'justify-start text-left',
    };
@endphp

<th {{ $attributes->merge([
        'class' => 'cursor-pointer select-none transition-colors hover:bg-black/15 focus:outline-none focus:bg-black/20 group'
    ]) }}
    wire:click="sortBy('{{ $field }}')"
    wire:keydown.enter="sortBy('{{ $field }}')"
    wire:keydown.space.prevent="sortBy('{{ $field }}')"
    tabindex="0"
    role="columnheader"
    aria-sort="{{ $ariaSort }}"
    title="Sort by {{ $slot }}">
    <div class="inline-flex items-center gap-1.5 {{ $alignmentClasses }} w-full">
        <span>{{ $slot }}</span>
        <span class="inline-flex shrink-0 items-center">
            @if ($isActive)
                @if ($direction === 'asc')
                    <x-icon name="chevron-up" class="w-4 h-4 text-white" />
                @else
                    <x-icon name="chevron-down" class="w-4 h-4 text-white" />
                @endif
            @else
                <x-icon name="chevron-up-down" class="w-4 h-4 text-white/40 group-hover:text-white/90 transition-opacity" />
            @endif
        </span>
    </div>
</th>
