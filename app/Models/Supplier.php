<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'photo_url',
        'cnpj',
        'state_registration',
        'cellphone',
        'phone',
        'responsible_person',
        'observation',
        'is_active',
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    public function address()
    {
        return $this->morphOne(Address::class, 'owner');
    }
}
