<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'fg_itam_transactions';

    protected $guarded = [];

    /**
     * Standardized badge pill class for transaction movement types.
     */
    public static function typePillClass(?string $type): string
    {
        $normalized = strtolower(trim($type ?? ''));

        return match ($normalized) {
            'assign' => 'pill normal',
            'transfer' => 'pill normal',
            'loan' => 'pill high',
            'return' => 'pill low',
            'repair', 'maintenance' => 'pill high',
            'retire', 'decommission' => 'pill closed',
            default => 'pill normal',
        };
    }

    /**
     * Attribute accessor for Eloquent instances.
     */
    public function getTypePillClassAttribute(): string
    {
        return self::typePillClass($this->type);
    }
}
