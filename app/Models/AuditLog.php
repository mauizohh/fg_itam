<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'fg_itam_audit_logs';
    protected $guarded = [];
}
