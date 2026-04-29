<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CashierInfo extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'charge',
        'close_time',
        'difference',
        'deposit',
        'observation_open',
        'observation_close'
    ];

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function cashierInfoFiles()
    {
        return $this->hasMany(CashierInfoFile::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getExpenses()
    {
        return Expense::with('expenseType', 'supplier', 'paymentMethod', 'supplier.address')
            ->where('payday', Carbon::parse($this->created_at)->toDateString())
            ->get();
    }
}
