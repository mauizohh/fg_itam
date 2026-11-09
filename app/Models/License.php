<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $table = 'fg_itam_licenses';
    protected $guarded = [];

    public function getStatusPillClassAttribute(): string
    {
        $status = strtolower($this->status);
        return match ($status) {
            'healthy' => 'pill low',
            'review' => 'pill high',
            'overallocated' => 'pill critical',
            default => 'pill normal',
        };
    }
}
