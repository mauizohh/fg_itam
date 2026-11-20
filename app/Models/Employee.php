<?php

namespace App\Models;

use App\Services\AdmidioDirectoryService;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'fg_itam_employees';

    protected $guarded = [];

    /**
     * Ensure employees are synced from Admidio.
     */
    public static function ensureSynced(): void
    {
        AdmidioDirectoryService::ensureSynced();
    }

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
