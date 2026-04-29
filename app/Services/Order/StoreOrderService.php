<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderByproduct;
use App\Models\Product;
use App\Models\CashierInfo;

class StoreOrderService
{
    /**
     * @var Order
     */
    private $order;

    /**
     * StoreOrderService constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param $data
     * @return array[]
     */
    public function run($data)
    {
        if(empty($data['client_id'])){
            $data['client_id'] = 1; // cliente final
        }

        $order = $this->order->create($data);
        $status = '';

        foreach ($data['products'] as $p) {
            if (!$status) {
                $status = 'waiting_payment';
            }

            // calcula a comissão a receber
            $prod = Product::find($p['id']);
            $commission = 0;
            $technician_commission = 0;
            $preco = round(($p['price'] * $p['amount']) - $p['discount'] + $p['addition'], 2);

            if($prod && $prod->can_commission){
                if($prod->commission_percentage > 0){
                    $commission = round($preco * $prod->commission_percentage / 100, 2);
                }

                if($prod->technician_commission_percentage > 0){
                    $technician_commission = round($preco * $prod->technician_commission_percentage / 100, 2);
                }
            }

            $product = $order->products()->create([
                'product_id' => $p['id'],
                'price' => $p['price'],
                'discount' => $p['discount'],
                'amount' => $p['amount'],
                'addition' => $p['addition'],
                'commission' => $commission,
                'commission_percentage' => $prod->commission_percentage,
                'technician_commission' => $technician_commission,
                'technician_commission_percentage' => $prod->technician_commission_percentage ?? 0,
                'cost' => $prod->cost,
                'profit' => round(($preco/$p['amount']) - $prod->cost - $commission - $technician_commission, 2),
            ]);

            $checklist_ids = $p['checklists'] ?? [];
            $product->product->checklists()->sync($checklist_ids);

            if ($p['id'] != '1' && $product->product && $product->product->quantity_in_stock == 0 && $product->product->type != 'sv') {
                $status = 'waiting_product';
            }

            if ($p['id'] == '1' && isset($p['maintenance_info'])) {
                $maintenance_info = $p['maintenance_info'];

                $maintenance_info['brand_id'] = is_array($p['brand_id']) ? $p['brand_id']['id'] : $p['brand_id'];
                $maintenance_info['brand_model_id'] = is_array($p['brand_model']) ? $p['brand_model']['id'] : $p['brand_model'];
                $maintenance_info['checklist'] = json_encode($p['checklists']);

                $product->maintenance()->create($maintenance_info);
                $status = 'maintenance';
            }

            if (isset($p['by_products']) && is_array($p['by_products'])) {
                foreach ($p['by_products'] as $byProduct) {
                    // calcula a comissão a receber
                    $prod = Product::find($byProduct['id']);
                    $addition = $byProduct['addition'];
                    $discount = $byProduct['discount'];
                    $amount = $byProduct['amount'];
                    $price = $byProduct['price'];
                    $preco = round(($price * $amount) - $discount + $addition, 2);
                    $commission = 0;
                    $technician_commission = 0;

                    if($prod && $prod->can_commission){
                        if($prod->commission_percentage > 0){
                            $commission = round($preco * $prod->commission_percentage / 100, 2);
                        }

                        if($prod->technician_commission_percentage > 0){
                            $technician_commission = round($preco * $prod->technician_commission_percentage / 100, 2);
                        }
                    }

                    $product->byProducts()->create([
                        'product_id' => $byProduct['id'],
                        'addition' => $addition,
                        'discount' => $discount,
                        'amount' => $amount,
                        'price' => $price,
                        'commission' => $commission,
                        'commission_percentage' => $prod->commission_percentage,
                        'technician_commission' => $technician_commission,
                        'technician_commission_percentage' => $prod->technician_commission_percentage ?? 0,
                        'cost' => $prod->cost,
                        'profit' => round(($preco/$amount) - $prod->cost - $commission, 2),
                    ]);

                    if($prod->quantity_in_stock == 0 && $prod->type != 'sv'){
                        $status = 'waiting_product';

                        $product->maintenance()->update([
                            'os_status' => 'waiting_stock'
                        ]);
                    }
                }
            }

            if (isset($p['maintenance_info'])) {
                $maintenance_info = $p['maintenance_info'];
                $os_status = $maintenance_info['os_status'] ?? '';

                if($status != 'waiting_product'){
                    if($os_status == 'waiting_approval'){
                        $status = 'waiting_approval';
                    } elseif($os_status == 'approved'){
                        $status = 'maintenance';
                    } elseif($os_status == 'finished'){
                        $status = 'waiting_payment';
                    }
                }
            }
        }

        if($order->status != 'is_budget'){ // orçamento
            $order->status = $status;
            $order->save();
        }

        $order->load('products', 'products.byProducts', 'coupon', 'coupons');

        return $order;
    }
}
