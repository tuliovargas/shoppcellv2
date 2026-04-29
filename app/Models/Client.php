<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\MaskValueTrait;
use Carbon\Carbon;
use App\Services\Utilities\Util;

class Client extends Model
{
    use HasFactory, SoftDeletes, MaskValueTrait;

    protected $fillable = [
        'full_name',
        'social_name',
        'gender',
        'photo_url',
        'cpf',
        'rg',
        'birthdate',
        'cellphone',
        'phone',
        'email',
        'observation',
        'profession',
        'is_active'
    ];

    public function address()
    {
        return $this->morphOne(Address::class, 'owner');
    }

    public function getCpfAttribute($value)
    {
        $value = Util::removeMask($value);

        if (str_starts_with($value, '000')) {
            $value = substr($value, 3);
        }

        if(strlen($value) == 14){
            return $this->mask($value, '##.###.###/####-##');
        }

        if(strlen($value) > 11){
            $value = strval(intval($value));// tirando os zeros
            $value = str_pad($value, 11, "0", STR_PAD_LEFT);
        }

        return $this->mask($value, '###.###.###-##');
    }

    public function getCellphoneAttribute($value)
    {
        return $this->mask($value, '(##) #####-####');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function allOpeneedValidCoupons(){
        return Coupon::join('orders', 'orders.id', '=', 'coupons.order_id')
            ->where('orders.client_id', $this->id)
            ->where('valido', true)
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->select('coupons.*')
            ->get();
    }
}
