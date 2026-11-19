@props([
    'align' => 'right',
    'width' => 'w-44',
])

@php
    $menuWidthPx = match($width) {
        'w-48' => 192,
        'w-40' => 160,
        'w-36' => 144,
        default => 176,
    };
@endphp

<div x-data="{
    open: false,
    dropdownStyle: '',
    id: Math.random().toString(36).substring(2, 9),
    toggle() {
        if (this.open) {
            this.close();
        } else {
            window.dispatchEvent(new CustomEvent('close-table-dropdowns', { detail: { id: this.id } }));
            this.calculatePosition();
            this.open = true;
            this.$nextTick(() => this.calculatePosition());
        }
    },
    close() {
        this.open = false;
    },
    calculatePosition() {
        const trigger = this.$refs.trigger;
        if (!trigger) return;
        const rect = trigger.getBoundingClientRect();
        const menu = this.$refs.menu;
        const menuWidth = menu ? menu.offsetWidth : {{ $menuWidthPx }};
        const menuHeight = menu ? menu.offsetHeight : 170;
        const gap = 4;
        
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceAbove = rect.top;
        
        let top;
        if (spaceBelow < menuHeight + gap && spaceAbove > spaceBelow) {
            top = Math.max(8, rect.top - menuHeight - gap);
        } else {
            top = Math.min(window.innerHeight - menuHeight - 8, rect.bottom + gap);
        }
        
        let left;
        @if($align === 'right')
            left = rect.right - menuWidth;
        @else
            left = rect.left;
        @endif
        
        if (left < 8) left = 8;
        if (left + menuWidth > window.innerWidth - 8) {
            left = window.innerWidth - menuWidth - 8;
        }
        
        this.dropdownStyle = `top: ${Math.round(top)}px; left: ${Math.round(left)}px;`;
    },
    init() {
        const onScroll = () => { if (this.open) this.close(); };
        window.addEventListener('scroll', onScroll, true);
        window.addEventListener('resize', onScroll);
        if (this.$cleanup) {
            this.$cleanup(() => {
                window.removeEventListener('scroll', onScroll, true);
                window.removeEventListener('resize', onScroll);
            });
        }
    }
}"
@close-table-dropdowns.window="if ($event.detail.id !== id) close()"
@keydown.escape.window="close()"
class="relative inline-block text-left">
    <div x-ref="trigger" @click="toggle()">
        {{ $trigger }}
    </div>

    <template x-teleport="body">
        <div x-show="open"
             x-ref="menu"
             x-cloak
             @click.outside="if (!$refs.trigger.contains($event.target)) close()"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             :style="dropdownStyle"
             class="fixed z-50 {{ $width }} bg-white rounded-lg shadow-xl border border-gray-200 p-1 text-xs font-medium focus:outline-none">
            {{ $slot }}
        </div>
    </template>
</div>
