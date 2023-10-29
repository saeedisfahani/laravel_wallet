<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Payments\PaymentStatus;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'status', 'currency_key'];
    protected $hidden = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'status' => PaymentStatus::class
    ];

    public function getRouteKeyName()
    {
        return 'unique_id';
    }

    protected static function booted()
    {
        static::creating(function ($payment) {
            $payment->unique_id = Str::uuid()->toString();
        });
    }
}
