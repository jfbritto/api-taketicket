<?php

namespace App\Models;

use App\Enums\BillingType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'asaas_id', 'status', 'billing_type', 'amount', 'paid_at'];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'billing_type' => BillingType::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
