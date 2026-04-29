<?php

namespace App\Services\Coupon;

use App\Exceptions\DeleteNotAllowedException;

class DeleteCouponService
{
    /**
     * @param $coupon
     * @return mixed
     * @throws DeleteNotAllowedException
     */
    public function run($coupon)
    {
        if ($coupon->orders && $coupon->orders->count())
            throw new DeleteNotAllowedException();

        return $coupon->delete();
    }
}
