<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssetIdGeneratorService
{
    /**
     * Map common categories to standardized 3-letter device codes.
     */
    protected static array $deviceCodes = [
        'laptop' => 'LAP',
        'desktop' => 'DSK',
        'monitor' => 'MON',
        'mobile' => 'MOB',
        'mobile device' => 'MOB',
        'phone' => 'MOB',
        'smartphone' => 'MOB',
        'server' => 'SRV',
        'network' => 'NET',
        'network equipment' => 'NET',
        'switch' => 'NET',
        'router' => 'NET',
        'access point' => 'NET',
        'printer' => 'PRN',
        'accessory' => 'ACC',
        'dock' => 'ACC',
    ];

    /**
     * Standardize category or input string to a 3-letter code.
     */
    public static function resolveDeviceCode(string $categoryOrCode): string
    {
        $clean = strtolower(trim($categoryOrCode));
        if (isset(self::$deviceCodes[$clean])) {
            return self::$deviceCodes[$clean];
        }

        // If already 3 uppercase letters, use it
        $upper = strtoupper(trim($categoryOrCode));
        if (strlen($upper) === 3 && ctype_alpha($upper)) {
            return $upper;
        }

        // Fallback: take first 3 alphabets
        $lettersOnly = preg_replace('/[^A-Za-z]/', '', $upper);

        return substr($lettersOnly.'DEV', 0, 3);
    }

    /**
     * Generate next Asset ID in format: IT-DEVICE(3 ALPHABET)-YEAR-NUMBER 5 series.
     * Example: IT-DSK-2026-00001
     */
    public static function generate(string $deviceCodeOrCategory, ?int $year = null): string
    {
        $code = self::resolveDeviceCode($deviceCodeOrCategory);
        $year = $year ?: (int) date('Y');

        $prefix = "IT-{$code}-{$year}-";

        // Query max number from fg_itam_assets
        $maxItam = Asset::where('id', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('id');

        // Also query fg_helpdesk_assets to prevent ID collisions with FG IT Helpdesk
        $maxHd = null;
        if (Schema::hasTable('fg_helpdesk_assets')) {
            $maxHd = DB::table('fg_helpdesk_assets')
                ->where('id', 'LIKE', "{$prefix}%")
                ->orderBy('id', 'desc')
                ->value('id');
        }

        $highest = 0;
        foreach ([$maxItam, $maxHd] as $candidate) {
            if ($candidate && preg_match('/-(\d{5})$/', $candidate, $m)) {
                $num = (int) $m[1];
                if ($num > $highest) {
                    $highest = $num;
                }
            }
        }

        $nextNumber = str_pad($highest + 1, 5, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextNumber}";
    }

    /**
     * Validate an Asset ID format.
     * Pattern: IT-[A-Z]{3}-[0-9]{4}-[0-9]{5}
     */
    public static function isValid(string $assetId): bool
    {
        return (bool) preg_match('/^IT-[A-Z]{3}-\d{4}-\d{5}$/', trim($assetId));
    }
}
