<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTPConfig extends Model
{
    use HasFactory;
    protected $table = 'otp_config';
    protected $guarded = [];
}
