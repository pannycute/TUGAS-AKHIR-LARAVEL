<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfirmations extends Model
{
    use HasFactory;

    protected $primaryKey = 'confirmation_id';

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'amount',
        'confirmation_date',
        'status',
        'proof_image',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'payment_method_id');
    }
}
