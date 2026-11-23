<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

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

    /**
     * Associated license asset pivot records.
     */
    public function licenseAssets(): HasMany
    {
        return $this->hasMany(LicenseAsset::class, 'license_id', 'id');
    }

    /**
     * Get or seed default assets associated with this license title.
     */
    public function getAssignedAssets(): Collection
    {
        if (! Schema::hasTable('fg_itam_license_assets')) {
            return collect();
        }

        $existing = LicenseAsset::with('asset')
            ->where(function ($q): void {
                $q->where('license_id', $this->id)
                    ->orWhere('software_name', $this->software_name);
            })
            ->get();

        if ($existing->isNotEmpty()) {
            return $existing->map(fn ($la) => $la->asset)->filter()->values();
        }

        // Seed default sample devices if none linked yet
        $defaultAssets = match ($this->software_name) {
            'Productivity Suite' => Asset::whereIn('category_id', ['LAP', 'DSK'])->take(5)->get(),
            'Endpoint Security' => Asset::take(6)->get(),
            'Design Suite' => Asset::where('department', 'Creative')->orWhere('category_id', 'LAP')->take(3)->get(),
            'CAD Professional' => Asset::where('category_id', 'DSK')->orWhere('brand', 'Dell')->take(2)->get(),
            default => Asset::take(3)->get(),
        };

        foreach ($defaultAssets as $ast) {
            LicenseAsset::firstOrCreate([
                'software_name' => $this->software_name,
                'asset_id' => $ast->id,
            ], [
                'license_id' => $this->id,
                'assigned_date' => now()->subMonths(rand(1, 6))->format('Y-m-d'),
                'status' => 'Active',
            ]);
        }

        return $defaultAssets;
    }
}
