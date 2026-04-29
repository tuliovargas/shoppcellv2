<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'purchase_invoice',
        'note',
        'supplier_id',
        'payment_method_id',
        'order_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'purchase_price');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
