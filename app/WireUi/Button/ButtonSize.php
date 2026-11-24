<?php

namespace App\WireUi\Button;

use WireUi\Components\Button\WireUi\Size\Base as WireUiBaseSize;

class ButtonSize extends WireUiBaseSize
{
    public function all(): array
    {
        return array_map(
            fn (string $classes) => "{$classes} font-semibold",
            parent::all()
        );
    }
}
