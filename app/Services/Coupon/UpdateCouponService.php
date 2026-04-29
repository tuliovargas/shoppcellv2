<?php

namespace App\Services\Coupon;

use App\Traits\FormatTrait;

class UpdateCouponService
{
    use FormatTrait;

    /**
     * @param $data
     * @param $coupon
     * @return mixed
     */
    public function run($data, $coupon)
    {
        $data['value'] = $this->brl2decimal($data['value']);
        $coupon->update($data);

        return $coupon;
    }
}
