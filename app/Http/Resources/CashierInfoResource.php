<?php

namespace App\Http\Resources;

use App\Models\Cashier;
use App\Models\CashierInfo;
use App\Models\PaymentMethod;
use App\Services\CashierInfo\IndexCashierInfoService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CashierInfoResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{

		$paymentMethods = PaymentMethod::orderBy('name')->get();
		$earnings = [];
		$totalVendas = 0;
		$totalDevolucao = 0;

		foreach ($paymentMethods as $key => $paymentMethod) {
			$payments = $this->payments()->where('payment_method_id', $paymentMethod->id)->get();

			$in = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
				->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
				->where('order_payments.payment_method_id', $paymentMethod->id)
				->where('cashiers.created_at', '>=', $this->created_at)
				->where(function ($query) {
					if ($this->close_time) {
						$query->where('cashiers.created_at', '<=', $this->close_time);
					}
				})
				->where('type', 'in')
				->select('order_payments.*')
				->get()
				->sum('value');

			$inCharge = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
				->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
				->where('order_payments.payment_method_id', $paymentMethod->id)
				->where('cashiers.created_at', '>=', $this->created_at)
				->where(function ($query) {
					if ($this->close_time) {
						$query->where('cashiers.created_at', '<=', $this->close_time);
					}
				})
				->where('type', 'in')
				->select('order_payments.*')
				->get()
				->sum('charge');

			$out = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
				->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
				->where('order_payments.payment_method_id', $paymentMethod->id)
				->where('cashiers.created_at', '>=', $this->created_at)
				->where(function ($query) {
					if ($this->close_time) {
						$query->where('cashiers.created_at', '<=', $this->close_time);
					}
				})
				->where('type', 'out')
				->select('order_payments.*')
				->get()
				->sum('value');

			$returned = 0;
			$canceledProducts = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
				->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
				->join('order_products', 'order_products.order_id', '=', 'orders.id')
				->where('cashiers.created_at', '>=', $this->created_at)
				->where(function ($query) {
					if ($this->close_time) {
						$query->where('cashiers.created_at', '<=', $this->close_time);
					}
				})
				->where('order_payments.payment_method_id', $paymentMethod->id)
				->whereNotNull('order_products.canceled_at')
				->where('type', 'in')
				->select('order_products.*')
				->get();

			foreach ($canceledProducts as $prod) {
				$preco = ($prod->price * $prod->amount) - $prod->discount + $prod->addition;
				$returned += round($preco, 2);
			}

			$totalDevolucao += $out;

			if ($paymentMethod->id == 3) { // dinheiro
				$totalDevolucao += $returned;
			}


			$earing = new \stdClass;
			$earing->id = $paymentMethod->id;
			$earing->payment_type = $paymentMethod->name;
			$earing->quantity_sales = $payments->count();
			$earing->total = round($in - $inCharge - $out, 2);
			$earing->difference = 0;
			$earing->total_devolvido = $returned;
			$earnings[] = $earing;


			$totalVendas += $payments->count();
		}

		$earlierCashier = CashierInfo::query()
			->whereNotNull('close_time')
			->orderBy('close_time', 'desc')
			->first();

		$earing = new \stdClass;
		$earing->id = null;
		$earing->payment_type = 'Troco do dia ' . Carbon::parse($this->created_at)->format('d/m/Y') . ' - ' . ($this->user ? $this->user->name : '');
		$earing->quantity_sales = $totalVendas;
		$earing->total = $earlierCashier ? $earlierCashier->charge : 0;
		$earing->difference = 0;
		$earnings[] = $earing;


		return [
			'id' => $this->id,
			'user_id' => $this->user_id,
			'charge' => $this->charge,
			'close_time' => $this->close_time,
			'deposit' => $this->deposit,
			'observation_open' => $this->observation_open,
			'observation_close' => $this->observation_close,
			'created_at' => $this->created_at,
			'user' => new UserResource($this->user),
			'orders' => OrderResource::collection($this->orders),
			'expenses' => ExpenseResource::collection($this->expenses),
			'cashierInfoFiles' => CashierInfoFileResource::collection($this->cashierInfoFiles),
			'earnings' => $earnings,
			'difference' => $this->difference
				? json_decode($this->difference)
				: [],
			'totalDevolvido' => $totalDevolucao,
		];
	}
}
