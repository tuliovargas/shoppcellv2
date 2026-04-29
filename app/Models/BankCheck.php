<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'check_number',
        'type_expense',
        'is_deposited',
        'value',
        'sale_id',
        'date_deposit',
        'bank_account_id',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
