<?php

namespace App\Services\CashierInfo;

use App\Models\CashierInfo;
use App\Models\CashierInfoFile;
use App\Models\Cashier;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\PaymentMethod;
use stdClass;
use Carbon\Carbon;

class CloseCashierInfoService
{
    /**
     * @var Cashier
     */
    private $cashier;

    /**
     * CloseCashierInfoService constructor.
     * @param CashierInfo $cashier
     */
    public function __construct(
        CashierInfo $cashier
    ) {
        $this->cashier = $cashier;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        DB::beginTransaction();
        try{
            $cashier = $this->cashier->where('close_time', null)->orderBy('created_at', 'desc')->first();
            if (!$cashier) return false;

            $data['observation_close'] = $data['observations'] ?? null;
            $data['close_time'] = now();

            $cashier->update($data);

            $files = $data['files'] ?? [];

            foreach($files as $file){
                $name = $file["name"];
                $extension = $file["extension"] != 'unknow' ? $file["extension"] : 'txt';
                $mimeType = $file["mimeType"] != 'unknow' ? $file["mimeType"] : 'text/plain';
                $dataFile = $file["data"] ?? '';

                if(strpos($dataFile, 'base64,') !== false){
                    $dataFile = explode('base64,', $dataFile)[1];
                }

                if(empty($dataFile)) continue;

                $path = '/cashier_infos/' . Str::random(40) . '.' . $extension;
                $dataFile = base64_decode($dataFile);
                Storage::put($path, $dataFile);

                $cashierInfoFile = CashierInfoFile::create([
                    'cashier_info_id' => $cashier->id,
                    'mime_type' => $mimeType,
                    'name' => $name,
                    'path' => $path,
                    'is_open' => false
                ]);
            }

            DB::commit();
        } catch(\Throwable | \Exception $e){
            DB::rollback();
            Log::error($e);

            abort(500);
        }

        $cashier->refresh();
        $cashier->load('user');
        $cashier->orders = Order::where('cashier_info_id', $cashier->id)
            ->whereIn('status', ['concluded', 'canceled', 'returned', 'partially_returned'])
            ->with(
                'coupon', 
                'coupons', 
                'products', 
                'products.product', 
                'products.product.checklists', 
                'products.byProducts', 
                'products.byProducts.product', 
                'client', 
                'user', 
                'comments', 
                'seller', 
                'client.address',
                'payments',
                'payments.payment_method'
            )->get();

        // agrupando so pagamentos
        $payments = [];
        foreach($cashier->orders as $order){
            $orderPayments = $order->payments;

            foreach($orderPayments as $orderPayment){
                $paymentMethod = $orderPayment->payment_method;

                if(!array_key_exists($paymentMethod->name, $payments)){
                    $payments[$paymentMethod->name] = [];
                    $payments[$paymentMethod->name]['total'] = 0;
                    $payments[$paymentMethod->name]['orders'] = [];
                }

                $payments[$paymentMethod->name]['total'] += $orderPayment->value - $orderPayment->charge;
                $payments[$paymentMethod->name]['orders'][] = $order;
            }
        }

        foreach($payments as $key=>$payment){
            $payments[$key]['total'] = round($payment['total'], 2);
        }

        $cashier->payments = $payments;
        $cashier->expenses = $cashier->getExpenses(); // carregando despesas dessa data
        $this->getEarning($cashier);

        return $cashier;
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
        $earing->payment_type = 'Troco do dia ' . Carbon::parse($cashier->created_at)->format('d/m/Y') . ' - ' . $cashier->user->name;
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
    }


}
