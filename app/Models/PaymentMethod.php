<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function orders()
    {
        return $this->belongsToMany(PaymentMethod::class)
            ->withPivot('value', 'tax_installment_id', 'pix_number', 'check_number', 'check_name', 'bank_id')
            ->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
