<?php

namespace App\Services\CashierInfo;

use App\Http\Resources\CashierInfoResource;
use App\Models\CashierInfo;
use App\Models\Cashier;
use App\Models\PaymentMethod;
use stdClass;
use Carbon\Carbon;

class IndexCashierInfoService
{
    /**
     * @var CashierInfo
     */
    private $cashier;

    /**
     * IndexCashierInfoService constructor.
     * @param CashierInfo $cashier
     */
    public function __construct(
        CashierInfo $cashier
    ) {
        $this->cashier = $cashier;
    }

    private function getEarning(CashierInfo &$cashier)
    {
        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $earnings = [];
        $totalVendas = 0;
        $totalDevolucao = 0;

        foreach ($paymentMethods as $key => $paymentMethod) {
            $payments = $cashier->payments()->where('payment_method_id', $paymentMethod->id)->get();

            $in = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
                ->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
                ->where('order_payments.payment_method_id', $paymentMethod->id)
                ->where('cashiers.created_at', '>=', $cashier->created_at)
                ->where(function ($query) use($cashier){
                    if($cashier->close_time){
                        $query->where('cashiers.created_at', '<=', $cashier->close_time);
                    }
                })
                ->where('type', 'in')
                ->select('order_payments.*')
                ->get()
                ->sum('value');

            $inCharge = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
                ->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
                ->where('order_payments.payment_method_id', $paymentMethod->id)
                ->where('cashiers.created_at', '>=', $cashier->created_at)
                ->where(function ($query) use($cashier){
                    if($cashier->close_time){
                        $query->where('cashiers.created_at', '<=', $cashier->close_time);
                    }
                })
                ->where('type', 'in')
                ->select('order_payments.*')
                ->get()
                ->sum('charge');

            $out = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
                ->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
                ->where('order_payments.payment_method_id', $paymentMethod->id)
                ->where('cashiers.created_at', '>=', $cashier->created_at)
                ->where(function ($query) use($cashier) {
                    if($cashier->close_time){
                        $query->where('cashiers.created_at', '<=', $cashier->close_time);
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
                ->where('cashiers.created_at', '>=', $cashier->created_at)
                ->where(function ($query) use($cashier){
                    if($cashier->close_time){
                        $query->where('cashiers.created_at', '<=', $cashier->close_time);
                    }
                })
                ->where('order_payments.payment_method_id', $paymentMethod->id)
                ->whereNotNull('order_products.canceled_at')
                ->where('type', 'in')
                ->select('order_products.*')
                ->get();

            foreach($canceledProducts as $prod){
                $preco = ($prod->price * $prod->amount) - $prod->discount + $prod->addition;
                $returned += round($preco, 2);
            }

            $totalDevolucao += $out;

            if($paymentMethod->id == 3){ // dinheiro
                $totalDevolucao += $returned;
            }



            $earing = new stdClass;
            $earing->id = $paymentMethod->id;
            $earing->payment_type = $paymentMethod->name;
            $earing->quantity_sales = $payments->count();
            $earing->total = round($in - $inCharge - $out, 2);
            $earing->difference = 0;
            $earing->total_devolvido = $returned;
            $earnings[] = $earing;

            $totalVendas += $payments->count();
        }

        $earlierCashier = $this->cashier
            ->whereNotNull('close_time')
            ->orderBy('close_time', 'desc')
            ->first();

        $earing = new \stdClass;
        $earing->id = null;
        $earing->payment_type = 'Troco do dia ' . Carbon::parse($cashier->created_at)->format('d/m/Y') . ' - ' . ($cashier && $cashier->user ? $cashier->user->name : '');
        $earing->quantity_sales = $totalVendas;
        $earing->total = $earlierCashier ? $earlierCashier->charge : 0;
        $earing->difference = 0;
        $earnings[] = $earing;

        $cashier->earnings = $earnings;
        $cashier->difference = $cashier->difference
            ? json_decode($cashier->difference)
            : [];

        $cashier->cashierInfoFiles; // carregando arquivos
        $cashier->totalDevolvido = $totalDevolucao;
        $cashier->load('expenses');
    }

	static function getEarningOtimized(CashierInfo &$cashier)
	{
		$paymentMethods = PaymentMethod::orderBy('name')->get();
		$earnings = [];
		$totalVendas = 0;
		$totalDevolucao = 0;

		foreach ($paymentMethods as $key => $paymentMethod) {
			$payments = $cashier->payments()->where('payment_method_id', $paymentMethod->id)->get();

			$in = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
				->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
				->where('order_payments.payment_method_id', $paymentMethod->id)
				->where('cashiers.created_at', '>=', $cashier->created_at)
				->where(function ($query) use($cashier){
					if($cashier->close_time){
						$query->where('cashiers.created_at', '<=', $cashier->close_time);
					}
				})
				->where('type', 'in')
				->select('order_payments.*')
				->get()
				->sum('value');

			$inCharge = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
				->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
				->where('order_payments.payment_method_id', $paymentMethod->id)
				->where('cashiers.created_at', '>=', $cashier->created_at)
				->where(function ($query) use($cashier){
					if($cashier->close_time){
						$query->where('cashiers.created_at', '<=', $cashier->close_time);
					}
				})
				->where('type', 'in')
				->select('order_payments.*')
				->get()
				->sum('charge');

			$out = Cashier::join('orders', 'orders.id', '=', 'cashiers.order_id')
				->join('order_payments', 'order_payments.order_id', '=', 'orders.id')
				->where('order_payments.payment_method_id', $paymentMethod->id)
				->where('cashiers.created_at', '>=', $cashier->created_at)
				->where(function ($query) use($cashier) {
					if($cashier->close_time){
						$query->where('cashiers.created_at', '<=', $cashier->close_time);
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
				->where('cashiers.created_at', '>=', $cashier->created_at)
				->where(function ($query) use($cashier){
					if($cashier->close_time){
						$query->where('cashiers.created_at', '<=', $cashier->close_time);
					}
				})
				->where('order_payments.payment_method_id', $paymentMethod->id)
				->whereNotNull('order_products.canceled_at')
				->where('type', 'in')
				->select('order_products.*')
				->get();

			foreach($canceledProducts as $prod){
				$preco = ($prod->price * $prod->amount) - $prod->discount + $prod->addition;
				$returned += round($preco, 2);
			}

			$totalDevolucao += $out;

			if($paymentMethod->id == 3){ // dinheiro
				$totalDevolucao += $returned;
			}



			$earing = new stdClass;
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
		$earing->payment_type = 'Troco do dia ' . Carbon::parse($cashier->created_at)->format('d/m/Y') . ' - ' . ($cashier && $cashier->user ? $cashier->user->name : '');
		$earing->quantity_sales = $totalVendas;
		$earing->total = $earlierCashier ? $earlierCashier->charge : 0;
		$earing->difference = 0;
		$earnings[] = $earing;

		$cashier->earnings = $earnings;
		$cashier->difference = $cashier->difference
			? json_decode($cashier->difference)
			: [];

		$cashier->totalDevolvido = $totalDevolucao;
	}

    /**
     * @return mixed
     */
    public function run($request)
    {
        $date_ini = $request->date_ini
            ? Carbon::parse($request->date_ini)->toDateTimeString()
            : Carbon::today()->subDays(30)->toDateTimeString();

        $date_fim = $request->date_fim
            ? Carbon::parse($request->date_fim)->endOfDay()->toDateTimeString()
            : Carbon::today()->endOfDay()->toDateTimeString();

        $query = CashierInfo::query();
        if (!isset($request['with-closed'])) {
            $query = $query->whereNull('close_time');
        }

        if (isset($request['id'])) {
            $query = $query->whereId($request['id']);
        }


        $cashiers = $query
            ->where('created_at', '>=', $date_ini)
            ->where(function ($query) use($date_fim) {
                $query->where('close_time', '<=', $date_fim)
                      ->orWhereNull('close_time');
            })
            ->orderBy('created_at', 'desc')->with( 'user',
						'orders',
						'orders.user',
						'orders.seller',
						'orders.client',
						'orders.payments',
						'orders.payments',
						'expenses',
						'orders.payments.payment_method')
            ->get();

        if(!$request->has('date_ini') ){
					if (isset($cashiers[0]))
            return new CashierInfoResource($cashiers[0]);
					else
						return new CashierInfoResource(new CashierInfo());
        }

        return CashierInfoResource::collection($cashiers);
    }
}
