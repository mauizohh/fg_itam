<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'fg_itam_purchase_orders';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];
}
