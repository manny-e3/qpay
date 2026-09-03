<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'path',
        'ip_address',
        'user_agent',
        'source_app',
        'user_email',
        'status_code',
        'duration',
    ];
}
