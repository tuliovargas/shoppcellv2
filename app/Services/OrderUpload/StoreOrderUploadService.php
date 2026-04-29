<?php

namespace App\Services\OrderUpload;

use App\Models\Order;

class StoreOrderUploadService
{
    /**
     * @var Order
     */
    private $order;

    /**
     * StoreOrderUploadService constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($order_id, $data)
    {
        $order = $this->order->findOrFail($order_id);

        $data['file_name'] = $data['file']->store('files/orders/' . $order_id);
        return $order->uploads()->create($data);
    }
}
