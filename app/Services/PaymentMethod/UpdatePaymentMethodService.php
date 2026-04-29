<?php

namespace App\Services\PaymentMethod;

class UpdatePaymentMethodService
{
    /**
     * @param $data
     * @param $paymentMethod
     * @return mixed
     */
    public function run($data, $paymentMethod)
    {
        $paymentMethod->update($data);

        return $paymentMethod;
    }
}
