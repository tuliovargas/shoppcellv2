<?php

namespace App\Services\Order;

class DeleteOrderService
{
    /**
     * @param $order
     * @return mixed
     */
    public function run($order)
    {
        $order->products()->delete();
        return $order->delete();
    }
}
