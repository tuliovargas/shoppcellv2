<?php

namespace App\Services\Order;

use App\Models\OrderProduct;
use App\Models\OrderByproduct;
use App\Models\CashierInfo;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateOrderService
{
    /**
     * @param $data
     * @param $order
     * @return array[]
     */
    public function run($data, $order)
    {
        DB::beginTransaction();
        try{
            if(isset($data['status']) && isset($data['products'][0]['maintenance']['os_status']) 
                && $data['status'] == 'waiting_payment' 
                && $data['products'][0]['maintenance']['os_status'] == 'finished'
                && empty($data['payments'])){
                    
                $data['created_at'] = Carbon::now();
            }

            $order->update($data);

            if (isset($data['products'])) {
                $productIds = [];

                foreach ($data['products'] as $key => $product) {
                    $preco = round(($product['price'] * $product['amount']) - $product['discount'] + $product['addition'], 2);
                    $commission = 0;
                    $technician_commission = 0;

                    $productDb = Product::find($product['product_id']);

                    if($productDb && $productDb->can_commission){
                        if($productDb->commission_percentage > 0){
                            $commission = round($preco * $productDb->commission_percentage / 100, 2);
                        }
        
                        if($productDb->technician_commission_percentage > 0){
                            $technician_commission = round($preco * $productDb->technician_commission_percentage / 100, 2);
                        }
                    }

                    $prod = OrderProduct::updateOrCreate(
                        [
                            'product_id' => $product['product_id'], 
                            'order_id' => $order->id
                        ], 
                        [
                            'price' => $product['price'],
                            'discount' => $product['discount'],
                            'amount' => $product['amount'],
                            'product_id' => $product['product_id'], 
                            'addition' => $product['addition'],
                            'discount' => $product['discount'],
                            'commission' => $commission,
                            'commission_percentage' => $productDb ? $productDb->commission_percentage : 0,
                            'technician_commission' => $technician_commission,
                            'technician_commission_percentage' => $productDb ? $productDb->technician_commission_percentage : 0
                        ]
                    );

                    if (isset($product['maintenance_info'])) {
                        $maintenance = $prod->maintenance;
                        $maintenance->os_status = $product['maintenance_info']["os_status"];
                        $maintenance->user_id = $product['maintenance_info']["user_id"];
                        $maintenance->save();
                    }

                    if (isset($product['by_products'])) {
                        $byProductIds = [];

                        foreach ($product['by_products'] as $key => $p) {
                            $byProductProduct = Product::find($p['product_id'] ?? $p['id']);
                            $preco = round(($p['price'] * $p['amount']) - $p['discount'] + $p['addition'], 2);
                            $commission = 0;
                            $technician_commission = 0;

                            if($byProductProduct && $byProductProduct->can_commission){
                                if($byProductProduct->commission_percentage > 0){
                                    $commission = round($preco * $byProductProduct->commission_percentage / 100, 2);
                                }
                
                                if($byProductProduct->technician_commission_percentage > 0){
                                    $technician_commission = round($preco * $byProductProduct->technician_commission_percentage / 100, 2);
                                }
                            }

                            $by_prod = OrderByproduct::updateOrCreate(
                                [
                                    'product_id' => $byProductProduct->id, 
                                    'order_product_id' => $prod->id
                                ], 
                                [
                                    'order_product_id' => $prod->id,
                                    'product_id' => $byProductProduct->id,
                                    'discount' => $p['discount'],
                                    'amount' => $p['amount'],
                                    'price' => $preco,
                                    'addition' => $p['addition'],
                                    'commission' => $commission,
                                    'commission_percentage' => $byProductProduct->commission_percentage,
                                    'technician_commission' => $technician_commission,
                                    'technician_commission_percentage' => $byProductProduct->technician_commission_percentage,
                                    'cost' => $byProductProduct->cost,
                                    'profit' => round(($preco/$p['amount']) - $prod->cost - $commission - $technician_commission, 2),
                                ]
                            );

                            $byProductIds[] = $by_prod->product_id;
                        }

                        $prod->byProducts()->whereNotIn('product_id', $byProductIds)->delete();
                    }

                    $productIds[] = $product['product_id'];
                }

                $order->products()
                    ->whereNotIn('product_id', $productIds)
                    ->WhereDoesntHave('maintenance')
                    ->WhereDoesntHave('byProducts')
                    ->delete();
            }

            DB::commit();
        } catch(\Exception | Trowable $e){
            DB::rollback();
            Log::error($e);

            abort(500);
        }

        return $order;
    }
}
