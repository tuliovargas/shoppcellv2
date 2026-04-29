<?php

namespace App\Services\Report;

use App\Models\OrderProduct;
use App\Models\Client;
use App\Models\Product;
use Carbon\Carbon;
use App\Services\Utilities\Util;
use Illuminate\Database\Eloquent\Builder;
use PDF;
use App\Models\Configuration;

class RequestsReportService
{
    /**
     * @var OrderProduct
     */
    private $orderProduct;

    /**
     * IndexOrderService constructor.
     * @param OrderProduct $orderProduct
     */
    public function __construct(OrderProduct $orderProduct)
    {
        $this->orderProduct = $orderProduct;
    }

    /**
     * @return mixed
     */
    public function run($data)
    {
        $is_pdf = $data['is_pdf'] ?? 0;
        $clientId = $data['client'] ?? null;
        $productId = $data['product'] ?? null;
        $startDate = $data['start_date'] ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $data['end_date'] ?? Carbon::today()->toDateString();

        $orderProducts = $this->orderProduct
            ->with('product', 'order')
            ->whereHas('order', function(Builder $query) use ($clientId) {
                $query->where('status', ['waiting_product']);

                if($clientId && $clientId > 0){
                    $query->where('client_id', $clientId);
                }
            })
            ->whereHas('product', function(Builder $query) use ($productId) {
                if($productId && $productId > 0){
                    $query->where('id', $productId);
                }
            })
            ->when($startDate != null, function ($query) use ($startDate){
                return $query->where('order_products.created_at', '>=', $startDate . ' 00:00:00');
            })
            ->when($endDate != null, function ($query) use ($endDate){
                return $query->where('order_products.created_at', '<=', $endDate . ' 23:59:59');
            })
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->where('products.quantity_in_stock', '<=', 0)
            ->orderBy('order_products.order_id')
            ->select('order_products.*')
            ->get();

        $requestsArr = [];

        foreach($orderProducts as $orderProduct){
            $obj = new \stdClass;
            $obj->orderId = $orderProduct->order_id;
            $obj->date = $orderProduct->order->date;
            $obj->client = $orderProduct->order->client ? $orderProduct->order->client->full_name : '';
            $obj->amount = $orderProduct->amount;
            $obj->product_name = $orderProduct->product->name;
            $obj->price = Util::doubleToString($orderProduct->price * $orderProduct->amount + $orderProduct->discount - $orderProduct->addition);

            $requestsArr[] = $obj;
        }

        if($is_pdf > 0){
            $company_name = Configuration::where('key', 'company_name')->first()->value;
            $address = Configuration::where('key', 'address')->first()->value;
            $cellphone = Configuration::where('key', 'cellphone')->first()->value;
            $email = Configuration::where('key', 'email')->first()->value;

            $client = Client::find($clientId);
            $product = Product::find($productId);

            $pdf = PDF::loadView('reports.pdf_layouts.requests', [
                'requests' => $requestsArr,
                'client' => $client ? $client->full_name : 'Todos os Clientes',
                'product' => $product ? $product->name : 'Todos os Produtos',
                'startDate' => $startDate ? Carbon::parse($startDate)->format('d/m/Y') : '',
                'endDate' => $startDate ? Carbon::parse($endDate)->format('d/m/Y') : '',
                'company_name' => $company_name,
                'address' => $address,
                'cellphone' => $cellphone,
                'email' => $email,
            ]);

            return $pdf->stream();
        }

        return $requestsArr;
    }
}
