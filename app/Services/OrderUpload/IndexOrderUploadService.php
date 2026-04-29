<?php

namespace App\Services\OrderUpload;

use App\Models\Order;

class IndexOrderUploadService
{
    /**
     * @var Order
     */
    private $order;

    /**
     * IndexOrderUploadService constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($order_id)
    {
        $order = $this->order->findOrFail($order_id);
        return $order->uploads()->all();
    }
}
