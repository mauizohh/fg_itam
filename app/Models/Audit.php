<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $table = 'fg_itam_audits';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    public function getStatusPillClassAttribute(): string
    {
        $status = strtolower($this->status);

        return match ($status) {
            'in progress' => 'pill normal',
            'completed' => 'pill low',
            'planned' => 'pill closed',
            default => 'pill normal',
        };
    }
}
