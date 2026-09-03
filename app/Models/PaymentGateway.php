<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'config', 'is_active'];

    protected $casts = [
        'config' => 'json',
        'is_active' => 'boolean',
    ];

    public function appGateways()
    {
        return $this->hasMany(AppPaymentGateway::class);
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
