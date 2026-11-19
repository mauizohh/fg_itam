<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Asset extends Model
{
    protected $table = 'fg_itam_assets';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected static function booted()
    {
        // Whenever an asset is saved, sync to fg_helpdesk_assets
        static::saved(function ($asset) {
            try {
                if (Schema::hasTable('fg_helpdesk_assets')) {
                    DB::table('fg_helpdesk_assets')->updateOrInsert(
                        ['id' => $asset->id],
                        [
                            'name' => $asset->name,
                            'assigned' => ! empty($asset->assigned_user) ? ($asset->assigned_user.(! empty($asset->department) ? ' ('.$asset->department.')' : '')) : 'Unassigned',
                            'assigned_user' => $asset->assigned_user ?? '',
                            'assigned_department' => $asset->department ?? '',
                            'serial' => $asset->serial_number ?? '',
                            'purchase' => $asset->purchase_date ? Carbon::parse($asset->purchase_date)->format('Y-m-d') : date('Y-m-d'),
                            'warranty' => $asset->warranty_end ? Carbon::parse($asset->warranty_end)->format('Y-m-d') : date('Y-m-d', strtotime('+3 years')),
                            'status' => $asset->status ?? 'Available',
                            'created_at' => $asset->created_at ?? now(),
                        ]
                    );
                }
            } catch (\Throwable $e) {
                // Silently handle if table not present in test mode
            }
        });
    }

    /**
     * Badge pill class for status.
     */
    public function getStatusPillClassAttribute(): string
    {
        $status = strtolower(trim($this->status ?? ''));

        return match ($status) {
            'assigned', 'active' => 'pill normal',
            'available' => 'pill low',
            'in repair', 'repair', 'repairing' => 'pill high',
            'retired', 'disposed', 'destroy' => 'pill closed',
            'missing', 'lost', 'stolen' => 'pill critical',
            default => 'pill normal',
        };
    }

    /**
     * Cleaned assigned user name for UI display without category suffixes.
     * When assigned_user is not set, displays department name instead of null/unassigned.
     */
    public function getDisplayAssignedUserAttribute(): ?string
    {
        if (! empty($this->assigned_user)) {
            $cleaned = User::cleanDisplayName($this->assigned_user);
            if ($cleaned !== '') {
                return $cleaned;
            }
        }

        if (! empty($this->department) && $this->status === 'Assigned') {
            return $this->department;
        }

        return ! empty($this->department) ? $this->department : null;
    }

    /**
     * Formatted assigned description.
     */
    public function getFormattedAssignedAttribute(): string
    {
        if (! empty($this->assigned_user)) {
            $displayUser = User::cleanDisplayName($this->assigned_user);
            if (! empty($this->department)) {
                return "{$displayUser} ({$this->department})";
            }

            return $displayUser;
        }

        if (! empty($this->department)) {
            return $this->department;
        }

        return $this->assigned_location ?: 'Unassigned';
    }

    /**
     * Check if warranty is expiring in 30 days.
     */
    public function isWarrantyExpiringSoon(): bool
    {
        if (empty($this->warranty_end)) {
            return false;
        }
        try {
            $end = Carbon::parse($this->warranty_end);

            return $end->isFuture() && $end->diffInDays(Carbon::now()) <= 30;
        } catch (\Throwable) {
            return false;
        }
    }
}
