<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * Canonical list of office location options.
     *
     * @var list<string>
     */
    public const OFFICE_OPTIONS = [
        'Jitra Office',
        'Kulim Office',
    ];

    protected $table = 'fg_itam_locations';

    protected $guarded = [];
}
