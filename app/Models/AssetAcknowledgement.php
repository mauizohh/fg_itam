<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAcknowledgement extends Model
{
    protected $table = 'fg_itam_asset_acknowledgements';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
        ];
    }
}
