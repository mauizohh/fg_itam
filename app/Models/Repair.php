<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $table = 'fg_itam_repairs';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    public function getStatusPillClassAttribute(): string
    {
        $status = strtolower($this->status);

        return match ($status) {
            'repairing' => 'pill high',
            'overdue' => 'pill critical',
            'awaiting part' => 'pill high',
            'completed' => 'pill low',
            default => 'pill normal',
        };
    }
}
