<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'fg_itam_audit_logs';

    protected $guarded = [];

    /**
     * Scope query strictly to asset-related activity.
     */
    public function scopeForAssets(Builder $query): Builder
    {
        return $query->where('object_type', 'Asset');
    }

    /**
     * Fallback alias if called as scopeForAssets().
     */
    public function scopeScopeForAssets(Builder $query): Builder
    {
        return $this->scopeForAssets($query);
    }

    /**
     * Clean a user or employee name by removing position/role labels, parenthetical info, or suffixes.
     * E.g. "Azril Employee (Test Staff)" -> "Azril Employee"
     *      "Amir Admin (System & IT Admin)" -> "Amir Admin"
     *      "Nora Support - IT Support" -> "Nora Support"
     */
    public static function cleanName(?string $raw): string
    {
        if ($raw === null || trim($raw) === '') {
            return '—';
        }

        $name = trim($raw);

        // Remove any content inside parentheses or brackets
        $name = preg_replace('/\s*[\(\[].*?[\)\]]/', '', $name) ?? $name;

        // Remove trailing dashes or role suffixes, e.g. " - IT Support"
        if (preg_match('/^([^\-,|]+)\s*[\-,|]\s*(?:IT|Support|Admin|Manager|Staff|Engineer|Lead|Officer|Director|Head|Technician|Finance|Auditor|Employee)/i', $name, $matches)) {
            $name = $matches[1];
        }

        $name = trim($name);

        return $name !== '' ? $name : '—';
    }

    /**
     * Accessor for clean actor name without role or position.
     */
    public function getCleanUserAttribute(): string
    {
        return self::cleanName($this->user);
    }

    /**
     * Resolve the target employee / custodian associated with this asset action.
     */
    public function resolveEmployeeName(?Asset $asset = null): string
    {
        // 1. Try to extract from after_state (e.g. "Assigned to Sarah Lim", "Loaned to Daniel Wong (Due: ...)", "Acknowledged by Azril Employee")
        if (! empty($this->after_state)) {
            if (preg_match('/(?:Assigned to|Loaned to|Transferred to|Acknowledged by)\s+([^\(\[\-,]+)/i', $this->after_state, $matches)) {
                $cleaned = self::cleanName($matches[1]);
                if ($cleaned !== '—') {
                    return $cleaned;
                }
            }
        }

        // 2. Try to extract from before_state (e.g. "Assigned to Sarah Lim", "Transferred from Amir Admin", "Returned by Nora Support")
        if (! empty($this->before_state)) {
            if (preg_match('/(?:Assigned to|Transferred from|Returned by)\s+([^\(\[\-,]+)/i', $this->before_state, $matches)) {
                $cleaned = self::cleanName($matches[1]);
                if ($cleaned !== '—') {
                    return $cleaned;
                }
            }
        }

        // 3. Fallback to Asset's assigned user
        if ($asset && ! empty($asset->assigned_user)) {
            return self::cleanName($asset->assigned_user);
        }

        return '—';
    }

    /**
     * Scope query for an employee to activities on their assigned assets or explicitly mentioning their name.
     *
     * @param  list<string>  $assetIds
     */
    public function scopeForEmployee(Builder $query, User $user, array $assetIds = []): Builder
    {
        $names = array_values(array_unique(array_filter([
            trim($user->name),
            trim($user->username),
        ])));

        return $query->where(function (Builder $q) use ($assetIds, $names): void {
            if (! empty($assetIds)) {
                $q->whereIn('object_id', $assetIds);
            }
            foreach ($names as $name) {
                $q->orWhere('after_state', 'like', "%{$name}%")
                    ->orWhere('before_state', 'like', "%{$name}%");
            }
        });
    }

    /**
     * Fallback alias if called as scopeForEmployee().
     */
    public function scopeScopeForEmployee(Builder $query, User $user, array $assetIds = []): Builder
    {
        return $this->scopeForEmployee($query, $user, $assetIds);
    }

    /**
     * Scope query for a department to activities on department assets or mentioning department name.
     *
     * @param  list<string>  $departments
     * @param  list<string>  $assetIds
     */
    public function scopeForDepartment(Builder $query, array $departments, array $assetIds = []): Builder
    {
        return $query->where(function (Builder $q) use ($assetIds, $departments): void {
            if (! empty($assetIds)) {
                $q->whereIn('object_id', $assetIds);
            }
            foreach ($departments as $dept) {
                $q->orWhere('after_state', 'like', "%{$dept}%")
                    ->orWhere('before_state', 'like', "%{$dept}%");
            }
        });
    }

    /**
     * Fallback alias if called as scopeForDepartment().
     */
    public function scopeScopeForDepartment(Builder $query, array $departments, array $assetIds = []): Builder
    {
        return $this->scopeForDepartment($query, $departments, $assetIds);
    }
}
