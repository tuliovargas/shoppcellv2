<?php

namespace App\Services\Maintenance;

use App\Models\Product;
use App\Models\OrderByproduct;

class UpdateMaintenanceService
{
    /**
     * @param Request $data
     * @param OrderProduct $orderProduct
     * @return mixed
     */
    public function run($data, $orderProduct)
    {
        if (isset($data['maintenance'])) {
            if(empty($data['maintenance']['os_status']) || is_numeric($data['maintenance']['os_status'])){
                $data['maintenance']['os_status'] = 'approved';
            }
            $fixed = $data['maintenance']['fixed'] ?? false;
            unset($data['maintenance']['fixed']);
            unset($data['maintenance']['order_product']);
            unset($data['maintenance']['brand']);
            unset($data['maintenance']['brand_model']);

            $orderProduct->maintenance()->update($data['maintenance']);

            if($data['maintenance']['os_status'] === 'no_maintenance'){
                $order = $orderProduct->order;
                $order->status = 'canceled';
                $order->save();
            } elseif($data['maintenance']['os_status'] === 'finished'){
                $order = $orderProduct->order;
                $order->status = 'waiting_payment';
                $order->save();
            }
        }

        $byProducts = [];
        if (isset($data['by_products']) && is_array($data['by_products'])) {
            foreach ($data['by_products'] as $byProduct) {
                if (empty($byProduct['id'])) {
                    // calcula a comissão a receber
                    $prod = Product::find($byProduct['product']['id']);
                    $addition = $byProduct['addition'] ?? 0;
                    $discount = $byProduct['discount'] ?? 0;
                    $amount = $byProduct['amount'] ?? 1;
                    $price = $byProduct['product']['price'];
                    $preco = round(($price * $amount) - $discount + $addition, 2);

                    $commission = 0;

                    if($prod && $prod->id > 1 && $prod->can_commission && $prod->commission_percentage > 0){
                        $commission = round($preco * $prod->commission_percentage / 100, 2);
                    }

                    $cost = !empty($prod->cost) ? $prod->cost : 0;

                    $productMaintenance = $orderProduct->byProducts()->create([
                        'order_product_id' => $orderProduct->id,
                        'product_id' => $byProduct['product']['id'],
                        'addition' => $addition,
                        'discount' => $discount,
                        'amount' => $amount,
                        'price' => $price,
                        'commission' => $commission,
                        'commission_percentage' => $prod->commission_percentage,
                        'cost' => $cost,
                        'profit' => round(($preco/$amount) - $prod->cost - $commission, 2),
                    ]);

                    $byProducts[] = $productMaintenance->id;
                } else{
                    $byProducts[] = $byProduct['id'];
                }
            }
            
            // removendo os que foram excluídos
            $orderProduct->byProducts()->whereNotIn('id', $byProducts)->delete();
        }

        $orderProduct->refresh();
        $maintenance = $orderProduct->maintenance;

        if($maintenance){
            $maintenance->load(
                'orderProduct', 
                'brand', 
                'brandModel', 
                'tecnician', 
                'orderProduct.product', 
                'orderProduct.byProducts',
                'orderProduct.order');
        }

        return $maintenance;
    }
}
