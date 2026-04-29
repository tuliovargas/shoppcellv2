<?php

namespace App\Services\Order;

use App\Models\CashierInfo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\OrderProduct;
use App\Models\OrderByproduct;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Cashier;
use App\Models\MaintenanceInfo;
use Carbon\Carbon;
use DB, Log, Auth;
use App\Services\Utilities\Util;
class RefundOrderService
{
    /**
     * @param $order
     * @return mixed
     */
    public function run($data, Order $order)
    {
        $products = $data['products'] ?? [];
        $by_products = $data['by_products'] ?? [];
        $observation = $data['observation'] ?? '';
        $user_id = $data['user_id'] ?? 1;
        $type = $data['type'] ?? 'total'; // partial/total
        $type_chargeback = $data['type_chargeback'] ?? 'cash'; // cash/coupon/none
        $status = $order->status;

        if(in_array($status, ['canceled', 'returned'])){
        	abort(400);
        }

        if(
			($status != 'concluded' && $status != 'partially_returned') ||
			$type_chargeback == 'none'
		){
        	// apenas cancela a ordem e retorna
        	$order->status = 'canceled';
        	$order->canceled_at = Carbon::now();
        	$order->cancellation_observation = $observation;
	        $order->save();

	        $productsDb = OrderProduct::where('order_id', $order->id)
	        	->whereNull('canceled_at')
	        	->get();

	        $byProducts = OrderByproduct::whereHas('orderProduct', function (Builder $query) use ($order){
				    $query->where('order_id', '=', $order->id);
				})
	        	->whereNull('order_byproducts.canceled_at')
	        	->select('order_byproducts.*')
	        	->get();

	        $allProducts = $productsDb->merge($byProducts);

	        foreach($allProducts as $product){
	        	$product->canceled_at = Carbon::now();
        		$product->canceled_user_id = $user_id;
        		$product->cancellation_observation = $observation;
        		$product->save();
	        }

			MaintenanceInfo::whereIn('order_product_id', $productsDb->pluck('id')->all())
				->update([
					'os_status' => 'cancelled'
				]);

			$order->load('payments', 'payments.payment_method');

	        return $order;
        }
        
        DB::beginTransaction();
        try{
        	$totalItens = 0;
			$productNames = [];

        	if($type == 'total'){
	        	if($order->canceled_at == null){
		        	$order->status = 'returned';
		        	$order->canceled_at = Carbon::now();
		        	$order->canceled_user_id = $user_id;
		        	$order->cancellation_observation = $observation;
		        	$order->save();

		        	$productsDb = OrderProduct::where('order_id', $order->id)
			        	->whereNull('canceled_at')
			        	->get();

			        $byProducts = OrderByproduct::whereHas('orderProduct', function (Builder $query) use ($order){
						    $query->where('order_id', '=', $order->id);
						})
			        	->whereNull('order_byproducts.canceled_at')
			        	->select('order_byproducts.*')
			        	->get();

			        $allProducts = $productsDb->merge($byProducts);

			        foreach($allProducts as $product){
			        	$product->canceled_at = Carbon::now();
		        		$product->canceled_user_id = $user_id;
		        		$product->cancellation_observation = $observation;
		        		$product->save();

			        	$prod = $product->product;
		        		$prod->quantity_in_stock = $prod->quantity_in_stock + $product->amount;
		        		$prod->save();

			        	$totalItens += round(($product->price * $product->amount) - $product->discount + $product->addition, 2);
						$productNames[] = $prod->name;
					}
		        } else{
		        	abort(400);
		        }
	        } else{
	        	foreach($products as $product){
		        	$productDb = OrderProduct::find($product['id']);

		        	if($productDb && $productDb->canceled_at == null){
		        		$productDb->canceled_at = Carbon::now();
		        		$productDb->canceled_user_id = $user_id;
		        		$productDb->cancellation_observation = $observation;
		        		$productDb->save();

		        		$prod = $productDb->product;
		        		$prod->quantity_in_stock = $prod->quantity_in_stock + $productDb->amount;
		        		$prod->save();

		        		$totalItens += round(($productDb->price * $productDb->amount) - $productDb->discount + $productDb->addition, 2);
						$productNames[] = $prod->name;
					}
		        }

		        foreach($by_products as $product){
		        	$productDb = OrderByproduct::find($product['id']);

		        	if($productDb && $productDb->canceled_at == null){
		        		$productDb->canceled_at = Carbon::now();
		        		$productDb->canceled_user_id = $user_id;
		        		$productDb->cancellation_observation = $observation;
		        		$productDb->save();

		        		$prod = $productDb->product;
		        		$prod->quantity_in_stock = $prod->quantity_in_stock + $productDb->amount;
		        		$prod->save();

		        		$totalItens += round(($productDb->price * $productDb->amount) - $productDb->discount + $productDb->addition, 2);
						$productNames[] = $prod->name;
					}
		        }

		        $productsDb = OrderProduct::where('order_id', $order->id)
		        	->whereNull('canceled_at')
		        	->count();

		        $byProducts = OrderByproduct::whereHas('orderProduct', function (Builder $query) use ($order){
					    $query->where('order_id', '=', $order->id);
					})
		        	->whereNull('order_byproducts.canceled_at')
		        	->count();

		        if($productsDb == 0 && $byProducts == 0){
		        	if($order->canceled_at == null){
		        		$order->status = 'returned';
			        	$order->canceled_at = Carbon::now();
			        	$order->canceled_user_id = $user_id;
			        	$order->cancellation_observation = $observation;
				        $order->save();
		        	}
		        } else{
		        	$order->status = 'partially_returned';
		        	$order->save();
		        }
	        }

	        if($totalItens > 0 && $type_chargeback == 'coupon'){
				$percDevolvido = $totalItens / ($order->total + $order->discount);

				// verificando se a compra foi realizada com cupom
				if($order->coupon_id){
					$orderCoupon = $order->coupon;
					$totalCupom = ($order->total * $percDevolvido) + ($orderCoupon->value * $percDevolvido);
				} else{
					$totalCupom = ($order->total + $order->discount) * $percDevolvido;
				}

	        	// criando o cupom
	        	$nroCupom = rand(100000, 999999);
				$name = explode(' ', $order->client->full_name)[0] ?? 'CUPOM';
				$name = strtoupper(Util::removerAcentos($name)) . $nroCupom;

	        	$coupon = Coupon::create([
			        'name' => $name,
			        'start_date' => Carbon::now(),
			        'end_date' => Carbon::now()->addYears(2),
			        'quantity' => 1,
			        'value' => round($totalCupom, 2),
			        'user_id' => $user_id,
			        'order_id' => $order->id
			    ]);
	        } elseif($type_chargeback  == 'cash' && Carbon::parse($order->created_at)->lt(Carbon::today())){
				// Lançando despesa no Caixa
				$percDevolvido = $totalItens / ($order->total + $order->discount);
				$totalDevolvido = ($order->total + $order->discount) * $percDevolvido;
				
				Cashier::create([
					'type' => 'out',
					'total_value' => $totalDevolvido,
					'note' => 'Lançamento criado referente à devolução do Pedido #' . $order->id . '. Cliente: ' . $order->client->full_name . '. Produtos: ' . implode(', ', $productNames),
					'order_id' => $order->id,
					'user_id' => Auth::user()->id
				]);
			}

	        $order->refresh();
			if($totalItens > 0 && $type_chargeback == 'coupon'){
				$order->cancellation_coupon = $coupon;
			}

			$order->total_devolvido = $totalItens;

        	DB::commit();
        } catch(\Exception $e){
        	DB::rollback();
        	Log::error($e);

        	return false;
        }

		$order->load('payments', 'payments.payment_method');
        return $order;
    }
}
