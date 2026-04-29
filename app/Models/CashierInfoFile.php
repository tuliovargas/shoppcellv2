<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierInfoFile extends Model
{
    use HasFactory;

    public $fillable = [
        'cashier_info_id',
        'mime_type',
        'name',
        'path',
        'is_open'
    ];

    public function cashierInfo()
    {
        return $this->belongsTo(CashierInfo::class);
    }
}
