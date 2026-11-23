<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseAsset extends Model
{
    protected $table = 'fg_itam_license_assets';

    protected $guarded = [];

    /**
     * Associated hardware asset.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }

    /**
     * Associated software license.
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id', 'id');
    }
}
