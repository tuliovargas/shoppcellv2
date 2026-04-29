<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use Carbon\Carbon;

class IndexCouponService
{
    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * IndexCouponService constructor.
     * @param Coupon $coupon
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';

        $query = $this->coupon
            ->where('quantity', '>', 0)
            ->whereDate('end_date', '>=', Carbon::now())
            ->when($search, function ($query, $search) {
                return $query->where('name', $search);
            });

        if ($request->paginate === 'false') {
            return $query->get()->first();
        }

        return $query->paginate(10);
    }
}
