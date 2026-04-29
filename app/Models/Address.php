<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'postcode',
        'street',
        'neighborhood',
        'number',
        'complement',
        'city',
        'state',
    ];

    protected $hidden = [
        'owner_id', 'owner_type'
    ];

    public function owner()
    {
        return $this->morphTo();
    }
}
