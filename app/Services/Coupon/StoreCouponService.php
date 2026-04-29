<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use App\Traits\FormatTrait;

class StoreCouponService
{
    use FormatTrait;

    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * StoreCouponService constructor.
     * @param Coupon $coupon
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        $data['value'] = $this->brl2decimal($data['value']);
        $data['user_id'] = auth()->user()->id;
        return $this->coupon->create($data);
    }
}
