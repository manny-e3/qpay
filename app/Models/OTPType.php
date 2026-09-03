<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTPType extends Model
{
    use HasFactory;
    protected $table = 'otp_type';
    protected $guarded = [];
}
