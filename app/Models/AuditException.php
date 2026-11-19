<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditException extends Model
{
    protected $table = 'fg_itam_audit_exceptions';

    protected $guarded = [];

    public function getPillClassAttribute(): string
    {
        $type = strtolower($this->exception_type);

        return match ($type) {
            'not found' => 'pill critical',
            'wrong location' => 'pill high',
            'damaged' => 'pill critical',
            'wrong user' => 'pill high',
            default => 'pill normal',
        };
    }
}
