<?php

namespace App\Services\CashierInfo;

use App\Models\CashierInfo;
use App\Models\PaymentMethod;
use stdClass;
use Carbon\Carbon;

class HistoryCashierInfoService
{
    /**
     * @var CashierInfo
     */
    private $cashier;

    /**
     * HistoryCashierInfoService constructor.
     * @param CashierInfo $cashier
     */
    public function __construct(
        CashierInfo $cashier
    )
    {
        $this->cashier = $cashier;
    }

    /**
     * @return mixed
     */
    public function run($request)
    {
       $limit = isset($request['limit']) ? $request['limit'] : 10;

        $cashiers = $this->cashier
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        $cashiersArr = [];
        if ($cashiers->isNotEmpty()) {
            foreach($cashiers as $cashier){
                $paymentMethods = PaymentMethod::orderBy('name')->get();
                $earnings = [];
                $totalVendas = 0;

                foreach ($paymentMethods as $key => $paymentMethod) {
                    $payments = $cashier->payments()->where('payment_method_id', $paymentMethod->id)->get();

                    $earing = new stdClass;
                    $earing->payment_type = $paymentMethod->name;
                    $earing->quantity_sales = $payments->count();
                    $earing->total = $payments->sum('value');
                    $earing->difference = 0;
                    $earnings[] = $earing;

                    $totalVendas += $payments->count();
                }

                $earing = new \stdClass;
                $earing->payment_type = 'Troco do dia ' . Carbon::parse($cashier->created_at)->format('d/m/Y') . " - " . $cashier->user->name;
                $earing->quantity_sales = $totalVendas;
                $earing->total = $cashier->charge;
                $earing->difference = 0;
                $earnings[] = $earing;

                $cashier->earnings = $earnings;
                $cashier->difference = $cashier->difference 
                    ? json_decode($cashier->difference) 
                    : [];
                $cashier->user; // carregando usuário
                $cashier->expenses = $cashier->getExpenses(); // carregando despesas dessa data

                $cashiersArr[] = $cashier;
            }
        }

        return $cashiersArr;
    }
}
