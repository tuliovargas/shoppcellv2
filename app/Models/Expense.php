<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'invoice',
        'payday',
        'value',
        'installments',
        'observation',
        'payment_method_id',
        'supplier_id',
        'expense_type_id',
        'cashier_info_id'
    ];

    public function cashierInfo()
    {
        return $this->belongsTo(CashierInfo::class);
    }

    public function receipts()
    {
        return $this->hasMany(ExpenseReceipt::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }

    public function getPaymentDateAttribute($value)
    {
        return Carbon::parse($this->payday)->format('d/m/Y');
    }
}
