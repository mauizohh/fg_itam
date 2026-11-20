<?php

namespace App\Models;

use App\Services\AdmidioDirectoryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Department extends Model
{
    protected $table = 'fg_itam_departments';

    protected $guarded = [];

    /**
     * Ensure departments are synced from Admidio.
     */
    public static function ensureSynced(): void
    {
        AdmidioDirectoryService::ensureSynced();
    }

    /**
     * Get the list of actual Admidio department names.
     *
     * @return list<string>
     */
    public static function admidioDepartmentNames(): array
    {
        return self::orderBy('name')->pluck('name')->all();
    }

    /**
     * Get all active Admidio departments.
     *
     * @return Collection<int, Department>
     */
    public static function admidioOptions(): Collection
    {
        self::ensureSynced();

        return self::orderBy('name')->get();
    }
}
