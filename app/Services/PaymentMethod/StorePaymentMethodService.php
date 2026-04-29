<?php

namespace App\Services\PaymentMethod;

use App\Models\PaymentMethod;

class StorePaymentMethodService
{
    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    /**
     * StorePaymentMethodService constructor.
     * @param PaymentMethod $paymentMethod
     */
    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        return $this->paymentMethod->create($data);
    }
}
