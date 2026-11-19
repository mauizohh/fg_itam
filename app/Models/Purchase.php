<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'fg_itam_purchases';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];
}
