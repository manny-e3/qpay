<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTPHistory extends Model
{
    use HasFactory;
    protected $table = 'otp_history';
    protected $guarded = [];
}
