<?php

namespace App\Services\Order;

use App\Models\Order;
use Carbon\Carbon;

class LastOrdersService
{
    /**
     * @return Order
     */
    public function run($data)
    {
        $clientId = $data['clientId'] ?? '';

        $order = Order::with('coupon', 'coupons', 'products', 'products.product', 
            'products.product.checklists', 'products.byProducts', 'products.byProducts.product', 
            'client', 'client.address', 'user', 'comments', 'seller',
            'products.maintenance', 'products.maintenance.brand', 'products.maintenance.brandModel',
            'products.maintenance.tecnician')

            ->when($clientId, function ($query, $clientId) {
                return $query->where('client_id', '=', $clientId);
            })
            ->where('orders.created_at', '>=', Carbon::now()->subDay())
            ->orderBy('orders.created_at', 'desc')
        		->first();

        return $order;
    }
}
