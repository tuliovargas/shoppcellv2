<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderByproduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_product_id',
        'product_id',
        'increase',
        'discount',
        'amount',
        'price',
        'addition',
        'commission',
        'commission_percentage',
        'technician_commission',
        'technician_commission_percentage',
        'cost',
        'profit',
        'canceled_at',
        'canceled_user_id',
        'cancellation_observation'
    ];

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function cancelled_user()
    {
        return $this->belongsTo(User::class, 'cancelled_user_id');
    }
}
