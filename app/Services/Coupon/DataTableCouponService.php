<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use Yajra\DataTables\DataTables;


class DataTableCouponService
{
    private $order;

    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    public function run($client_id)
    {
        $model = $this->coupon
            ->select(
                'id',
                'name',
                'quantity',
                'value',
                'start_date',
                'end_date',
            );

        return Datatables::of($model)
            ->addIndexColumn()
            ->editColumn('value', function ($coupon) {
                return 'R$ ' . number_format($coupon->value, 2, ',', '.');
            })
            ->editColumn('start_date', function ($coupon) {
                return date('d/m/Y', strtotime($coupon->start_date));
            })
            ->editColumn('end_date', function ($coupon) {
                return date('d/m/Y', strtotime($coupon->start_date));
            })
            ->addColumn('action', function ($row) {
                $editButton = "<a href=\"" . route('coupons.edit', ['coupon' => $row->id]) . "\"><i class=\"mx-2 fas fa-pen\"></i></a>";
                $deleteButton = "<a href=\"" . route('coupons.delete', ['coupon' => $row->id]) . "\"><i class=\"mx-2 fas fa-trash\"></i></a>";
                return $editButton . $deleteButton;
            })
            ->make(true);
    }
}
