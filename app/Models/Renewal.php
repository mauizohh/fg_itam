<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    protected $table = 'fg_itam_renewals';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function getStatusPillClassAttribute(): string
    {
        $status = strtolower($this->status);
        return match ($status) {
            'approval needed' => 'pill high',
            'in review' => 'pill normal',
            'planned' => 'pill low',
            'approved' => 'pill low',
            default => 'pill normal',
        };
    }
}
