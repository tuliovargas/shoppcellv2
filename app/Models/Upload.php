<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'uploadable_id',
        'uploadable_type',
    ];

    protected $hidden = [
        'id',
        'uploadable_id',
        'uploadable_type',
    ];

    public function uploadable()
    {
        return $this->morphTo();
    }
}
