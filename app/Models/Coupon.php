<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'quantity',
        'value',
        'user_id',
        'order_id',
        'total_usado',
        'valido'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }
}
