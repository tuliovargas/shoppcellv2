<?php

namespace App\Services\PaymentMethod;

use App\Exceptions\DeleteNotAllowedException;

class DeletePaymentMethodService
{
    /**
     * @param $paymentMethod
     * @return mixed
     * @throws DeleteNotAllowedException
     */
    public function run($paymentMethod)
    {
        if ($paymentMethod->orders->count() > 0 || $paymentMethod->expenses->count() > 0) {
            throw new DeleteNotAllowedException();
        }
        return $paymentMethod->delete();
    }
}
