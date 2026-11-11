@props([
    'title',
])

<div class="space-y-3">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-lg font-heading font-bold text-ink">{{ $title }}</h2>

        @isset($badge)
            <div class="text-xs font-medium px-3 py-1 bg-gray-100 text-ink rounded-full border border-gray-200">
                {{ $badge }}
            </div>
        @endisset

        @isset($actions)
            <div>
                {{ $actions }}
            </div>
        @endisset
    </div>

    <x-card padding="none" class="overflow-hidden border border-gray-200 shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            {{ $slot }}
        </div>
    </x-card>

    @isset($pagination)
        <div class="mt-4">
            {{ $pagination }}
        </div>
    @endisset
</div>
