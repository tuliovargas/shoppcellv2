<?php

namespace App\Services\Order;

use App\Models\Order;
use Yajra\DataTables\DataTables;


class DataTableOrderService
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function run($client_id)
    {
        $model = $this->order
            ->join('users as sellers', 'sellers.id', '=', 'orders.seller_id')
            ->join('users as caixas', 'caixas.id', '=', 'orders.user_id')

            ->select(
                'orders.id',
                'orders.created_at',
                'orders.total',
                'sellers.name as seller',
                'caixas.name as user',
                'orders.status'
            );

        if ($client_id) {
            $model = $model->where('client_id', $client_id);
        }

        return Datatables::of($model)
            ->addIndexColumn()
            ->make(true);
    }
}
