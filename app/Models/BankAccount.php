<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'agency',
        'account_number',
        'is_active',
        'bank_id',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function bankChecks()
    {
        return $this->hasMany(BankCheck::class);
    }
}
