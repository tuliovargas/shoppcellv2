<?php

namespace App\Services\Order;

use App\Models\Configuration;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class IndexOrderService
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * IndexOrderService constructor.
     * @param Order $order
     * @param Configuration $configuration
     */
    public function __construct(Order $order, Configuration $configuration)
    {
        $this->order = $order;
        $this->configuration = $configuration;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';
        
        if(isset($request['clientId']) && !isset($request['date_ini'])){
            $request['date_ini'] = $this->order->select('created_at')->where('client_id', $request['clientId'])->orderBy('created_at', 'DESC')->first();
            if($request['date_ini'])
            {
                $request['date_ini'] = $request['date_ini']->created_at->toDateString();
            }else{
                $request['date_ini'] = $request['date_ini'] ?? Carbon::today()->startOfWeek()->toDateString();
            }
        }else if(isset($request['search']) && is_numeric($request['search'])){
            $request['date_ini'] = $this->order->select('created_at')->where('id', $search)->first()->created_at;
        }else if(isset($request['date_ini'])){
            $request['date_ini'] = date('Y-m-d', strtotime($request['date_ini']));
        }else{
            $request['date_ini'] = $request['date_ini'] ?? Carbon::today()->startOfWeek()->toDateString();
        }
        $request['date_fim'] = $request['date_fim'] ?? Carbon::today()->toDateString();

        $query = $this->order->with('coupon', 'coupons', 'products', 'products.product', 
            'products.maintenance', 'products.maintenance.brand', 'products.maintenance.brandModel', 
            'products.maintenance.tecnician', 'products.product.checklists', 'products.byProducts', 
            'products.byProducts.product', 'client', 'user', 'comments', 'seller', 'client.address',
            'payments', 'payments.payment_method', 'cashierInfo',
            
            // ordem associada à ordem principal. No caso de uma garantia, por exemplo
            'order', 'order.products', 'order.products.product', 'order.products.maintenance', 
            'order.products.maintenance.brand', 'order.products.maintenance.brandModel', 
            'order.products.maintenance.tecnician', 'order.products.product.checklists', 
            'order.products.byProducts', 'order.products.byProducts.product', 'order.seller', 'order.payments', 
            'order.payments.payment_method')
            
            ->when(!empty($search), function ($query) use($search){
                return $query->whereHas('client', function($query2) use($search) {
                    return $query2->where('full_name', 'like', '%' . $search . '%');
                })->orWhereHas('products', function($query) use($search) {
                    return $query->whereHas('product', function($query) use($search) {
                        return $query->where('name', 'like', '%' . $search . '%');
                    });
                })->orWhere('orders.id', $search);
            })
            ->when($request['maintenanceStatus'], function ($query, $maintenanceStatus) {
                return $query->where('status', '=', $maintenanceStatus);
            })
            ->when($request['status'], function ($query, $statuses) {
                return $query->whereIn('status', explode(',', $statuses));
            })
            ->when($request['no_maintenance'], function ($query) {
                $query->where(function ($query) {
                    return $query->doesntHave('products')
                        ->orwhereHas('products', function (Builder $query) {
                            $query->doesntHave('maintenance')->orWhereHas('maintenance', function (Builder $query) {
                                $query->where('maintenance_infos.os_status', 'finished');
                            });
                        });
                });
            })
            ->when($request['no_orcamento'], function ($query, $statuses) {
                return $query->where(function ($query) {
                    $query->whereNotNull('user_id')
                        ->whereNotIn('status', ['waiting_approval', 'is_budget']);
                });
            })
            ->when($request['no_canceled'], function ($query) {
                return $query->where('status', '!=', 'canceled');
            })
            ->when($request['clientId'], function ($query, $clientId) {
                return $query->where('client_id', $clientId);
            })
            ->when($request['clientName'], function ($query, $clientName) {
                return $query->whereHas('client', function (Builder $query) use ($clientName) {
                    $query->where('full_name', 'like', '%' . $clientName . '%');
                });
            })
            ->when($request['order_by'], function ($query) use ($request){
                return $query->orderBy('orders.' . $request['order_by'], $request['order']);
            })
            ->when($request['date_ini'], function ($query) use ($request){
                $dateIni = Carbon::parse($request['date_ini'])->startOfDay();
                return $query->where('orders.created_at', '>=', $dateIni);
            })
            ->when($request['date_fim'], function ($query) use ($request){
                $dateFim = Carbon::parse($request['date_fim'])->endOfDay();
                return $query->where('orders.created_at', '<=', $dateFim);
            });

        $validateBudgetInDays = $this->configuration->where('key', 'budget')
            ->value('value'); 

        foreach ($query->get() as $order) {
            if ($order->seller_id == null 
                && $order->created_at->addDays($validateBudgetInDays) <= Carbon::now()) {
                $order->status = 'canceled';
                $order->save();
            }
        }

        if ($request->last === 'true') {
            $lastOrder = $this->order->all()
                ->last();
            return [
                'last_order' => $lastOrder,
                'orders' => $query->get(),
            ];
        }

        if ($request->paginate === 'false') {
            return $query->get();
        }

        return $query->paginate(10);
    }
}
