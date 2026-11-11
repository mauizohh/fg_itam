@props([
    'title',
    'value',
    'subtitle' => null,
])

<article {{ $attributes->merge(['class' => 'group rounded-xl p-[17px] bg-gradient-to-br from-[var(--theme-primary-dark,#8f2428)] to-[var(--theme-primary-deep,#5c1416)] shadow-[0_16px_32px_rgba(0,0,0,0.15),0_4px_8px_rgba(0,0,0,0.08)] hover:-translate-y-1 hover:shadow-[0_24px_48px_rgba(0,0,0,0.2),0_8px_16px_rgba(0,0,0,0.1)] transition-all duration-200 border-0 text-white']) }}>
    <span class="block font-bold text-sm text-white font-sans">{{ $title }}</span>
    <strong class="block text-[32px] leading-tight font-extrabold font-heading text-white mt-2">{{ $value }}</strong>
    @if($subtitle)
        <small class="block text-xs text-white/80 mt-1 font-sans">{!! $subtitle !!}</small>
    @elseif($slot->isNotEmpty())
        <small class="block text-xs text-white/80 mt-1 font-sans">{{ $slot }}</small>
    @endif
</article>
