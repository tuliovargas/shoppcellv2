<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    public $fillable = [
        'order_id',
        'payment_method_id',
        'value',
        'tax_installment_id',
        'brand_card',
        'pix_number',
        'check_number',
        'check_name',
        'bank_id',
        'cashier_info_id',
        'charge'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
