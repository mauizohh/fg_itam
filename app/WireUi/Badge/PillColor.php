<?php

namespace App\WireUi\Badge;

use WireUi\Enum\Packs\Color;
use WireUi\Support\ComponentPack;

class PillColor extends ComponentPack
{
    public function default(): string
    {
        return config('wireui.style.color') ?? Color::BASE;
    }

    public function all(): array
    {
        return [
            Color::BASE => 'text-slate-700 bg-slate-500/10 border border-slate-500/30 dark:text-slate-300 dark:bg-slate-500/20 dark:border-slate-500/40',
            Color::PRIMARY => 'text-fg-red bg-red-500/10 border border-red-500/30 dark:text-red-300 dark:bg-red-500/15 dark:border-red-500/40',
            Color::SECONDARY => 'text-slate-700 bg-slate-500/10 border border-slate-500/30 dark:text-slate-300 dark:bg-slate-500/20 dark:border-slate-500/40',
            Color::POSITIVE => 'text-emerald-700 bg-emerald-500/10 border border-emerald-500/35 dark:text-emerald-300 dark:bg-emerald-500/15 dark:border-emerald-500/40',
            Color::NEGATIVE => 'text-red-700 bg-red-500/10 border border-red-500/35 dark:text-red-300 dark:bg-red-500/15 dark:border-red-500/40',
            Color::WARNING => 'text-amber-700 bg-amber-500/15 border border-amber-500/40 dark:text-amber-300 dark:bg-amber-500/20 dark:border-amber-500/40',
            Color::INFO => 'text-blue-700 bg-blue-500/10 border border-blue-500/35 dark:text-blue-300 dark:bg-blue-500/15 dark:border-blue-500/40',

            Color::DARK => 'text-slate-700 bg-slate-500/15 border border-slate-600/30 dark:text-slate-200 dark:bg-slate-700/60 dark:border-slate-500/40',
            Color::WHITE => 'text-slate-700 bg-white/80 border border-slate-300 dark:text-slate-200 dark:bg-slate-800 dark:border-slate-600',
            Color::BLACK => 'text-slate-800 bg-slate-200 border border-slate-300 dark:text-slate-200 dark:bg-slate-800 dark:border-slate-600',
            Color::SLATE => 'text-slate-700 bg-slate-500/10 border border-slate-500/30 dark:text-slate-300 dark:bg-slate-500/20 dark:border-slate-500/40',
            Color::GRAY => 'text-gray-700 bg-gray-500/10 border border-gray-500/30 dark:text-gray-300 dark:bg-gray-500/20 dark:border-gray-500/40',
            Color::ZINC => 'text-zinc-700 bg-zinc-500/10 border border-zinc-500/30 dark:text-zinc-300 dark:bg-zinc-500/20 dark:border-zinc-500/40',
            Color::NEUTRAL => 'text-neutral-700 bg-neutral-500/10 border border-neutral-500/30 dark:text-neutral-300 dark:bg-neutral-500/20 dark:border-neutral-500/40',
            Color::STONE => 'text-stone-700 bg-stone-500/10 border border-stone-500/30 dark:text-stone-300 dark:bg-stone-500/20 dark:border-stone-500/40',
            Color::RED => 'text-red-700 bg-red-500/10 border border-red-500/35 dark:text-red-300 dark:bg-red-500/15 dark:border-red-500/40',
            Color::ORANGE => 'text-orange-700 bg-orange-500/15 border border-orange-500/40 dark:text-orange-300 dark:bg-orange-500/20 dark:border-orange-500/40',
            Color::YELLOW => 'text-yellow-700 bg-yellow-500/15 border border-yellow-500/40 dark:text-yellow-300 dark:bg-yellow-500/20 dark:border-yellow-500/40',
            Color::AMBER => 'text-amber-700 bg-amber-500/15 border border-amber-500/40 dark:text-amber-300 dark:bg-amber-500/20 dark:border-amber-500/40',
            Color::LIME => 'text-lime-700 bg-lime-500/15 border border-lime-500/40 dark:text-lime-300 dark:bg-lime-500/20 dark:border-lime-500/40',
            Color::GREEN => 'text-emerald-700 bg-emerald-500/10 border border-emerald-500/35 dark:text-emerald-300 dark:bg-emerald-500/15 dark:border-emerald-500/40',
            Color::EMERALD => 'text-emerald-700 bg-emerald-500/10 border border-emerald-500/35 dark:text-emerald-300 dark:bg-emerald-500/15 dark:border-emerald-500/40',
            Color::TEAL => 'text-teal-700 bg-teal-500/10 border border-teal-500/35 dark:text-teal-300 dark:bg-teal-500/15 dark:border-teal-500/40',
            Color::CYAN => 'text-cyan-700 bg-cyan-500/10 border border-cyan-500/35 dark:text-cyan-300 dark:bg-cyan-500/15 dark:border-cyan-500/40',
            Color::SKY => 'text-sky-700 bg-sky-500/10 border border-sky-500/35 dark:text-sky-300 dark:bg-sky-500/15 dark:border-sky-500/40',
            Color::BLUE => 'text-blue-700 bg-blue-500/10 border border-blue-500/35 dark:text-blue-300 dark:bg-blue-500/15 dark:border-blue-500/40',
            Color::INDIGO => 'text-indigo-700 bg-indigo-500/10 border border-indigo-500/35 dark:text-indigo-300 dark:bg-indigo-500/15 dark:border-indigo-500/40',
            Color::VIOLET => 'text-violet-700 bg-violet-500/10 border border-violet-500/35 dark:text-violet-300 dark:bg-violet-500/15 dark:border-violet-500/40',
            Color::PURPLE => 'text-purple-700 bg-purple-500/10 border border-purple-500/35 dark:text-purple-300 dark:bg-purple-500/15 dark:border-purple-500/40',
            Color::FUCHSIA => 'text-fuchsia-700 bg-fuchsia-500/10 border border-fuchsia-500/35 dark:text-fuchsia-300 dark:bg-fuchsia-500/15 dark:border-fuchsia-500/40',
            Color::PINK => 'text-pink-700 bg-pink-500/10 border border-pink-500/35 dark:text-pink-300 dark:bg-pink-500/15 dark:border-pink-500/40',
            Color::ROSE => 'text-rose-700 bg-rose-500/10 border border-rose-500/35 dark:text-rose-300 dark:bg-rose-500/15 dark:border-rose-500/40',
        ];
    }
}
