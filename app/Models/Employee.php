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

    /**
     * Refresh employee asset and pending return counts from actual assets table.
     */
    public static function syncAssetCounts(): void
    {
        app(AdmidioDirectoryService::class)->syncEmployeeAssetCounts();
    }

    /**
     * Real-time assigned assets count matching EmployeeDetail logic.
     */
    public function getAssignedAssetsCountAttribute($value): int
    {
        if (isset($this->attributes['assigned_assets_count_computed'])) {
            return (int) $this->attributes['assigned_assets_count_computed'];
        }

        return Asset::query()
            ->where(function ($query): void {
                $query->where('assigned_user', $this->name)
                    ->orWhere('assigned_user', 'like', "%{$this->name}%");
            })
            ->whereIn('status', ['Assigned', 'Loan'])
            ->count();
    }

    /**
     * Real-time pending returns count matching EmployeeDetail logic.
     */
    public function getPendingReturnsCountAttribute($value): int
    {
        if (isset($this->attributes['pending_returns_count_computed'])) {
            return (int) $this->attributes['pending_returns_count_computed'];
        }

        return Asset::query()
            ->where(function ($query): void {
                $query->where('assigned_user', $this->name)
                    ->orWhere('assigned_user', 'like', "%{$this->name}%");
            })
            ->where(function ($query): void {
                $query->where('status', 'Loan')
                    ->orWhereNotNull('expected_return_date');
            })
            ->count();
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
