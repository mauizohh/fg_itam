<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'fg_itam_employees';

    protected $guarded = [];

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        $initials = '';
        foreach ($parts as $p) {
            $initials .= strtoupper(substr($p, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }

        return $initials ?: 'EM';
    }
}
