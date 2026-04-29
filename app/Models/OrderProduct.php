<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'parent_id',
        'product_id',
        'price',
        'discount',
        'amount',
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

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function maintenance()
    {
        return $this->hasOne(MaintenanceInfo::class);
    }

    public function byProducts()
    {
        return $this->hasMany(OrderByproduct::class);
    }

    public function cancelled_user()
    {
        return $this->belongsTo(User::class, 'cancelled_user_id');
    }
}
