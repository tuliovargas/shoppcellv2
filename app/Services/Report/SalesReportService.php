<?php

namespace App\Services\Report;

use App\Helpers;
use App\Models\Order;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use App\Services\Utilities\Util;
use PDF;
use App\Models\Configuration;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    /**
     * @var Order
     */
    private $order;

    /**
     * IndexOrderService constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function run($data)
    {
        if(isset($data['annualReport'])){
            return self::rawReportSql($data['month'], $data['year']);
        }
        $is_pdf = $data['is_pdf'] ?? 0;
        $clientId = $data['client'] ?? null;
        $sellerId = $data['seller'] ?? null;
        $status = $data['status'] ?? null;
        $startDate = $data['start_date'] ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $data['end_date'] ?? Carbon::today()->toDateString();

        $orders = $this->order
            ->where(function ($query) use ($status) {
                if($status && $status != '0'){
                    $query->where('status', $status);
                } else{
                    $query->whereIn('status', ['concluded', 'canceled', 'partially_returned', 'returned', 'waiting_product', 'waiting_payment']);
                }
            })
            ->where(function ($query) use ($sellerId) {
                if($sellerId && $sellerId > 0){
                    $query->where('seller_id', $sellerId);
                }
            })
            ->where(function ($query) use ($clientId) {
                if($clientId && $clientId > 0){
                    $query->where('client_id', $clientId);
                }
            })
            ->when($startDate != null, function ($query) use ($startDate){
                return $query->where('created_at', '>=', $startDate . ' 00:00:00');
            })
            ->when($endDate != null, function ($query) use ($endDate){
                return $query->where('created_at', '<=', $endDate . ' 23:59:59');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $ordersArr = [];

        foreach($orders as $order){
            $commission = 0;

            foreach($order->products as $product){
                $commission += $product->commission;
                $commission += $product->technician_commission;
                $commission += $product->byProducts->sum('commission');
                $commission += $product->byProducts->sum('technician_commission');
            }

            $obj = new \stdClass;
            $obj->orderId = $order->id;
            $obj->date = $order->date;
            $obj->client = $order->client ? $order->client->full_name : '';
            $obj->discount = Util::doubleToString($order->discount);
            $obj->commission = Util::doubleToString($commission);
            $obj->price = Util::doubleToString($order->total);
            $obj->status = $order->getStatus();
            $obj->seller = $order->seller ? $order->seller->name : '';
            $obj->pagamento = $order->payments->isNotEmpty() 
                ? $order->payments->first()->payment_method->name
                : '---';

            $ordersArr[] = $obj;
        }

        if($is_pdf > 0){
            $company_name = Configuration::where('key', 'company_name')->first()->value;
            $address = Configuration::where('key', 'address')->first()->value;
            $cellphone = Configuration::where('key', 'cellphone')->first()->value;
            $email = Configuration::where('key', 'email')->first()->value;

            $client = Client::find($clientId);
            $seller = User::find($sellerId);
            $total = $orders->sum('total');

            $pdf = PDF::loadView('reports.pdf_layouts.sales', [
                'orders' => $ordersArr,
                'client' => $client ? $client->full_name : 'Todos do Clientes',
                'seller' => $seller ? $seller->name : 'Todos os Vendedores',
                'status' => $status != '0' ? $this->parseStatus($status) : 'Todas as Situações',
                'startDate' => $startDate ? Carbon::parse($startDate)->format('d/m/Y') : '',
                'endDate' => $startDate ? Carbon::parse($endDate)->format('d/m/Y') : '',
                'company_name' => $company_name,
                'address' => $address,
                'cellphone' => $cellphone,
                'email' => $email,
                'total' => Util::doubleToString($total),
            ]);

            return $pdf->stream();
        }

        return $ordersArr;
    }

    public function parseStatus($status){
        $allStatus = [
            'is_request' => 'Orçamento',
            'waiting_approval' => 'Aguardando Aprovação do Cliente', 
            'approved' => 'Aprovado pelo Cliente', 
            'waiting_product' => 'Aguardando Produto', 
            'maintenance' => 'Em Manutenção', 
            'waiting_payment' => 'Aguardando Pagamento',
            'concluded' => 'Concluído',
            'canceled' => 'Cancelado', 
            'returned' => 'Devolvido', 
            'waiting_maintenance' => 'Aguardando Manutenção', 
            'partially_returned' => 'Parcialmente Devolvido'
        ];

        return $allStatus[$status] ?? 'Indeterminado';
    }
    private static function rawReportSql($month, $year)
    {
        $sqlCashier = Helpers::unionReport('orders', 'total', 'created_at', $month, $year);
        $sqlExpenses = Helpers::unionReport('expenses', 'value', 'created_at', $month, $year);
        $sqlCommission = Helpers::unionReport('order_products', 'commission', 'created_at', $month, $year);
        $sqlCosts = Helpers::unionReport('order_products', 'cost', 'created_at', $month, $year, 'SUM(cost * amount)');
        $sqlDiscounts = Helpers::unionReport('order_products', 'discount', 'created_at', $month, $year);

        $totalCashier = DB::select(DB::raw($sqlCashier));
        $totalExpenses = DB::select(DB::raw($sqlExpenses));
        $totalCommissions = DB::select(DB::raw($sqlCommission));
        $totalCosts = DB::select(DB::raw($sqlCosts));
        $totalDiscounts = DB::select(DB::raw($sqlDiscounts));

        $totals = [];

        for ($i = 0; $i < count($totalCashier); $i++) {
            $totals[$i]['total'] = round(
                $totalCashier[$i]->total - ($totalCommissions[$i]->total + $totalCosts[$i]->total + $totalDiscounts[$i]->total + $totalExpenses[$i]->total),
                2
            );
        }

        return [
            'totals' => array_column($totals, 'total')
        ];
    }

}
