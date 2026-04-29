<?php

namespace App\Services\PaymentMethod;

use App\Models\PaymentMethod;

class IndexPaymentMethodService
{
    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    /**
     * IndexPaymentMethodService constructor.
     * @param PaymentMethod $paymentMethod
     */
    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run()
    {
        $query = $this->paymentMethod;
        return $query->all();
    }
}
