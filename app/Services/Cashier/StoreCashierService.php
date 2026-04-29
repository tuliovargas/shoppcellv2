<?php

namespace App\Services\Cashier;

use App\Models\Cashier;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\CashierInfo;
use App\Services\Coupon\UpdateCouponService;
use App\Services\Order\UpdateOrderService;
use App\Services\Stock\StoreStockService;

class StoreCashierService
{
    /**
     * @var Cashier
     */
    private $cashier;

    /**
     * @var CashierInfo
     */
    private $cashierInfo;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var UpdateOrderService
     */
    private $updateOrderService;

    /**
     * @var UpdateCouponService
     */
    private $updateCouponService;

    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * @var StoreStockService
     */
    private $storeStockService;

    /**
     * StoreCashierService constructor.
     * @param Cashier $cashier
     * @param CashierInfo $cashierInfo
     * @param Order $order
     * @param Coupon $coupon
     * @param UpdateOrderService $updateOrderService
     * @param UpdateCouponService $updateCouponService
     * @param StoreStockService $storeStockService
     */
    public function __construct(
        Cashier $cashier,
        Order $order,
        Coupon $coupon,
        CashierInfo $cashierInfo,
        UpdateOrderService $updateOrderService,
        UpdateCouponService $updateCouponService,
        StoreStockService $storeStockService
    ) {
        $this->cashier = $cashier;
        $this->cashierInfo = $cashierInfo;
        $this->order = $order;
        $this->coupon = $coupon;
        $this->updateOrderService = $updateOrderService;
        $this->updateCouponService = $updateCouponService;
        $this->storeStockService = $storeStockService;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        $cashier = $this->cashier->create($data);

        $cashierInfo = $this->cashierInfo->where('close_time', null)->orderBy('created_at', 'desc')->first();
        if (!$cashierInfo) return null;

        if ($data['type'] === 'in') {
            $order = $this->order->find($data['order_id']);

            if (isset($data['coupon_id'])) {
                $coupon = Coupon::find($data['coupon_id']);
                $couponData['value'] = $coupon->value;

                if($coupon->order_id != null){ // Cupom de devolução
                    $couponData['total_usado'] = min($coupon->total_usado + $order->discount, $coupon->value);
                    $couponData['valido'] = $coupon->value > $couponData['total_usado'];
                } else{
                    $oldQuantityCoupon = $coupon->quantity;
                    $couponData['valido'] = $couponData['quantity'] > 0;
                    $couponData['quantity'] = $oldQuantityCoupon - 1;
                }

                $this->updateCouponService->run($couponData, $coupon);
            }

            $data['status'] = 'concluded';
            $data['cashier_info_id'] = $cashierInfo->id;

            $this->updateOrderService->run($data, $order);

            $total = $cashier->total_value;

            foreach ($data['payment_methods'] as $paymentMethod) {
                if ($paymentMethod['tax_installment_id'] == 0) {
                    $paymentMethod['tax_installment_id'] = null;
                }

                $order->payments()->create([
                    'payment_method_id' => $paymentMethod['id'],
                    'value' => $paymentMethod['value'],
                    'charge' => $paymentMethod['charge'] ?? 0.0,
                    'tax_installment_id' => $paymentMethod['tax_installment_id'],
                    'brand_card' => isset($paymentMethod['brand_card']) ? $paymentMethod['brand_card'] : null,
                    'pix_number' => isset($paymentMethod['pix_number']) ? $paymentMethod['pix_number'] : null,
                    'check_number' => isset($paymentMethod['check_number']) ? $paymentMethod['check_number'] : null,
                    'check_name' => isset($paymentMethod['check_name']) ? $paymentMethod['check_name'] : null,
                    'bank_id' => isset($paymentMethod['bank_id']) ? $paymentMethod['bank_id'] : null,
                    'cashier_info_id' => $cashierInfo->id,
                ]);
            }

            $this->storeStockService->run($data);
        }

        return $cashier;
    }
}
