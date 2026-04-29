<?php

namespace App\Services\Maintenance;

use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Builder;
use Auth;
use Carbon\Carbon;

class IndexMaintenanceService
{
    /**
     * @var Product
     */
    private $product;

    /**
     * IndexProductService constructor.
     * @param Product $product
     */
    public function __construct(OrderProduct $order)
    {
        $this->order = $order;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';
        $client_id = isset($request['client_id']) ? $request['client_id'] : '';
		$allMaintenances = isset($request['all']);
        $queryStatus = isset($request['query_status']) ? $request['query_status'] : [];
        $last = isset($request['last']);
        $order_field = isset($request['order_field']) ? $request['order_field'] : 'orders.id';
        $order = isset($request['order']) ? $request['order'] : 'asc';
        $date_ini = $request['date_ini'] ? Carbon::parse($request['date_ini']) : Carbon::today()->startOfWeek();
        $date_fim = $request['date_fim'] ? Carbon::parse($request['date_fim']) : Carbon::today();
        $id = isset($request['maintenance_id']) ? $request['maintenance_id'] : null;
        
        $fields = ['product', 'product.checklists', 'maintenance', 'maintenance.brand', 'maintenance.brandModel', 'order', 'order.payments', 'byProducts', 'byProducts.product', 'order.client', 'order.client.address', 'order.comments', 'order.uploads', 'order.coupon', 'maintenance.orderProduct', 'maintenance.orderProduct.product'];

        $query = $this->order
            ->with($fields)
            ->where('product_id', 1)
            ->whereHas('maintenance', function (Builder $query) use($allMaintenances, $queryStatus, $id) {
                if($id){
                    $query->where('id', '=', $id);
                }
				if($allMaintenances) {
					return $query;
				} elseif(!empty($queryStatus)){
                    return $query->whereIn('os_status', $queryStatus);
                }

				$query->whereIn('os_status', ['approved', 'maintenance', 'fixed', 'waiting_approval', 'waiting_stock']);
            })
            ->whereHas('order', function (Builder $query) use($last) {
				if($last) {
					return $query->with(['order','order.payments'])->where('user_id', Auth::user()->id);
				}
            })
            ->when($client_id, function ($query, $client_id) {
                return $query->whereHas('order', function (Builder $query) use ($client_id) {
                    $query->where('client_id', $client_id);
                });
            })
            ->when(!empty($search), function ($query) use ($search){
                return $query->whereHas('order', function($query) use($search) {
                    return $query->whereHas('client', function($query2) use($search) {
                        return $query2->where('clients.full_name', 'like', '%' . $search . '%');
                    })->orWhere('id', $search);
                })->orWhereHas('product', function($query) use($search) {
                    return $query->where('products.name', 'like', '%' . $search . '%');
                })->orWhereHas('maintenance', function($query) use($search) {
                    return $query->where('issue', 'like', '%' . $search . '%')
                        ->orWhere('technical_report', 'like', '%' . $search . '%')
                        ->orWhereHas('brand', function($query) use($search) {
                            return $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('brandModel', function($query) use($search) {
                            return $query->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($date_ini, function ($query) use ($date_ini){
                $dateIni = $date_ini->startOfDay();
                return $query->where('orders.created_at', '>=', $dateIni);
            })
            ->when($date_fim, function ($query) use ($date_fim){
                $dateFim = $date_fim->endOfDay();
                return $query->where('orders.created_at', '<=', $dateFim);
            })
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('clients', 'clients.id', '=', 'orders.client_id')
            ->join('maintenance_infos', 'maintenance_infos.order_product_id', '=', 'order_products.id')
            ->leftJoin('brands', 'brands.id', '=', 'maintenance_infos.brand_id')
            ->leftJoin('users', 'users.id', '=', 'maintenance_infos.user_id')
            ->select('order_products.*');

        if ($request->paginate === 'false') {
            if($last){
                return [
                    $query->orderBy('created_at', 'desc')->first()
                ];
            } else{
                if($order_field && $order){
                    $query = $query->orderBy($order_field, $order);
                }

                $query = $query->get();
                $manutencoes = [];

                foreach($query as $q){
                    $q->id = $q->order_id;
                    $q->coupons = $q->order->client->allOpeneedValidCoupons();
                    $manutencoes[] = $q;
                }

                return $manutencoes;
            }
        }

        return $query->paginate(10);
    }
}
