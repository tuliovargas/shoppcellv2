<?php

namespace App\Services\Report;

use App\Helpers;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use App\Services\Utilities\Util;
use PDF;
use App\Models\Configuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class CommissionReportService
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
        $user = User::find($data['user']);
        $startDate = $data['start_date'] ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $data['end_date'] ?? Carbon::today()->toDateString();
        $roles = $user->roles->pluck('name')->all();

        $orders = $this->order
            ->where('status', 'concluded')
            ->where(function ($query) use ($user, $roles) {
                if(!in_array('técnico', $roles)){
                    $query->where('seller_id', $user->id);
                } else{
                    $query->whereHas('products', function (Builder $query) use ($user) {
                        $query->whereHas('maintenance', function (Builder $query) use ($user) {
                            $query->where('maintenance_infos.user_id', $user->id);
                        });
                    });
                }
            })
            ->when($startDate != null, function ($query) use ($startDate){
                return $query->where('created_at', '>=', $startDate . ' 00:00:00');
            })
            ->when($endDate != null, function ($query) use ($endDate){
                return $query->where('created_at', '<=', $endDate . ' 23:59:59');
            })
            ->orderBy('created_at')
            ->get();

        $productsArr = [];

        foreach($orders as $order){
            $orderProducts = $order->products;

            foreach($orderProducts as $orderProduct){
                if(in_array('técnico', $roles) || in_array('vendedor', $roles) || in_array('admin', $roles)){
                    if($orderProduct->commission > 0 && (in_array('vendedor', $roles) || in_array('admin', $roles))){
                        $product = $orderProduct->product;
                        $price = ($orderProduct->price * $orderProduct->amount) - $orderProduct->discount + $orderProduct->addition;

                        $obj = new \stdClass;
                        $obj->id = $orderProduct->id;
                        $obj->orderId = $order->id;
                        $obj->date = $order->date;
                        $obj->name = $product->name;
                        $obj->price = Util::doubleToString($price);
                        $obj->percentage = Util::doubleToString($orderProduct->commission_percentage);
                        $obj->commission = Util::doubleToString($orderProduct->commission, 2);
                        $obj->commission_payed = $orderProduct->commission_payed;
                        $obj->product = $orderProduct;

                        $productsArr[] = $obj;
                    }
                    
                    if($orderProduct->technician_commission > 0 && (in_array('técnico', $roles) || in_array('admin', $roles))){
                        $product = $orderProduct->product;
                        $price = ($orderProduct->price * $orderProduct->amount) - $orderProduct->discount + $orderProduct->addition;

                        $obj = new \stdClass;
                        $obj->id = $orderProduct->id;
                        $obj->orderId = $order->id;
                        $obj->date = $order->date;
                        $obj->name = $product->name;
                        $obj->price = Util::doubleToString($price);
                        $obj->percentage = Util::doubleToString($orderProduct->technician_commission_percentage);
                        $obj->commission = Util::doubleToString($orderProduct->technician_commission, 2);
                        $obj->commission_payed = $orderProduct->technician_commission_payed;
                        $obj->product = $orderProduct;

                        $productsArr[] = $obj;
                    }
                }

                if(in_array('técnico', $roles) || in_array('vendedor', $roles) || in_array('admin', $roles)){
                    $byProducts = $orderProduct->byProducts;

                    foreach($byProducts as $byProduct){
                        if($byProduct->commission > 0 && (in_array('vendedor', $roles) || in_array('admin', $roles))){
                            $product = $byProduct->product;
                            $price = ($byProduct->price * $byProduct->amount) - $byProduct->discount + $byProduct->addition;

                            $obj = new \stdClass;
                            $obj->id = $byProduct->id;
                            $obj->orderId = $order->id;
                            $obj->date = $order->date;
                            $obj->name = $product->name;
                            $obj->price = Util::doubleToString($price);
                            $obj->percentage = Util::doubleToString($byProduct->commission_percentage);
                            $obj->commission = Util::doubleToString($byProduct->commission, 2);
                            $obj->commission_payed = $byProduct->commission_payed;
                            $obj->product = $byProduct;

                            $productsArr[] = $obj;
                        } 
                        
                        if($byProduct->technician_commission > 0 && (in_array('técnico', $roles)|| in_array('admin', $roles))){
                            $product = $byProduct->product;
                            $price = ($byProduct->price * $byProduct->amount) - $byProduct->discount + $byProduct->addition;

                            $obj = new \stdClass;
                            $obj->id = $byProduct->id;
                            $obj->orderId = $order->id;
                            $obj->date = $order->date;
                            $obj->name = $product->name;
                            $obj->price = Util::doubleToString($price);
                            $obj->percentage = Util::doubleToString($byProduct->technician_commission_percentage);
                            $obj->commission = Util::doubleToString($byProduct->technician_commission, 2);
                            $obj->commission_payed = $byProduct->technician_commission_payed;
                            $obj->product = $byProduct;

                            $productsArr[] = $obj;
                        }
                    } 
                }
            }
        }

        return $productsArr;
    }

    public function process($data){
        $products = $data['products'];
        $user = User::find($data['user']);
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        $roles = $user->roles->pluck('name')->all();

        $cashiers = $this->run($data);
        $produtos = [];
        $total = 0;

        foreach($cashiers as $cashier){
            $product = $cashier->product;

            if(in_array($product->id, $products)){
                if(in_array('técnico', $roles) || in_array('admin', $roles)){
                    $product->technician_commission_payed = true;
                }
                
                if(in_array('vendedor', $roles) || in_array('admin', $roles)){
                    $product->commission_payed = true;
                }

                $product->save();

                $produtos[] = $cashier;
                $total += Util::stringToDouble($cashier->commission);
            }
        }

        // Criação do Relatório
        $company_name = Configuration::where('key', 'company_name')->first()->value;
        $address = Configuration::where('key', 'address')->first()->value;
        $cellphone = Configuration::where('key', 'cellphone')->first()->value;
        $email = Configuration::where('key', 'email')->first()->value;

        $pdf = PDF::loadView('reports.pdf_layouts.commission', [
            'cashier' => $produtos, 
            'total' => Util::doubleToString($total),
            'colaborador' => $user->name,
            'startDate' => $startDate ? Carbon::parse($startDate)->format('d/m/Y') : '',
            'endDate' => $endDate ? Carbon::parse($endDate)->format('d/m/Y') : '',
            'company_name' => $company_name,
            'address' => $address,
            'cellphone' => $cellphone,
            'email' => $email,
        ]);

        return $pdf->stream();
    }
    private static function rawReportSql($month, $year)
    {
        $sql = Helpers::unionReport('order_products', 'commission', 'created_at', $month, $year);
        $totals = DB::select(DB::raw($sql));
        return [
            'totals' => array_column($totals, 'total')
        ];
    }
}
