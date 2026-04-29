<?php

namespace App\Services\Stock;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Services\Product\UpdateProductService;

class StoreStockService
{
    /**
     * @var Stock
     */
    private $stock;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var UpdateProductService
     */
    private $updateProductService;

    /**
     * StoreStockService constructor.
     * @param Stock $stock
     * @param Order $order
     * @param UpdateProductService $updateProductService
     */
    public function __construct(Stock $stock, Order $order, UpdateProductService $updateProductService)
    {
        $this->stock = $stock;
        $this->order = $order;
        $this->updateProductService = $updateProductService;
    }

    /**
     * @param $data
     * @return array[]
     */
    public function run($data)
    {
        $stock = $this->stock->create($data);
        
        if(!isset($data['products'])){
            $data['products'] = [];
        }

        if ($data['order_id']) {
            $order = $this->order->find($data['order_id']);
            $data['products'] = $order->products;
        }

        foreach ($data['products'] as $orderProduct) {
            $stock->products()->attach([
                $stock->id => [
                    'product_id' => $orderProduct->product_id,
                    'quantity' => $orderProduct->amount,
                    'purchase_price' => $orderProduct->price,
                ]
            ]);

            if ($data['type'] === 'in') {
                $product = $orderProduct->product;
                $oldQuantity = $product->quantity_in_stock;
                $dataProduct['quantity_in_stock'] = max($oldQuantity - $orderProduct->amount, 0);
                $this->updateProductService->run($dataProduct, $product);
            }
        }

        return $stock;
    }
}
