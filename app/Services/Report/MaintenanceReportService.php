<?php

namespace App\Services\Report;

use App\Models\MaintenanceInfo;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;
use App\Services\Utilities\Util;
use Illuminate\Database\Eloquent\Builder;
use PDF;
use App\Models\Configuration;

class MaintenanceReportService
{
    /**
     * @var MaintenanceInfo
     */
    private $order;

    /**
     * IndexOrderService constructor.
     * @param MaintenanceInfo $order
     */
    public function __construct(MaintenanceInfo $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    /**
     * @return mixed
     */
    public function run($data)
    {
        $is_pdf = $data['is_pdf'] ?? 0;
        $clientId = $data['client'] ?? null;
        $tecnicianId = $data['tecnician'] ?? null;
        $status = $data['status'] ?? null;
        $startDate = $data['start_date'] ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $data['end_date'] ?? Carbon::today()->toDateString();

        $maintenances = $this->maintenance
            ->join('order_products', 'order_products.id', '=', 'maintenance_infos.order_product_id')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->where(function ($query) use ($status) {
                if($status && $status != '0'){
                    $query->where('maintenance_infos.os_status', $status);
                }
            })
            ->where(function ($query) use ($clientId) {
                if($clientId && $clientId > 0){
                    $query->where('orders.client_id', $clientId);
                }
            })
            ->where(function ($query) use ($tecnicianId) {
                if($tecnicianId && $tecnicianId > 0){
                    $query->where('maintenance_infos.user_id', $tecnicianId);
                }
            })
            ->when($startDate != null, function ($query) use ($startDate){
                return $query->where('orders.created_at', '>=', $startDate . ' 00:00:00');
            })
            ->when($endDate != null, function ($query) use ($endDate){
                return $query->where('orders.created_at', '<=', $endDate . ' 23:59:59');
            })
            ->select('maintenance_infos.*', 'orders.created_at')
            ->distinct()
            ->orderBy('orders.created_at', 'desc')
            ->get();

        $ordersArr = [];

        foreach($maintenances as $maintenance){
            $orderProduct = $maintenance->orderProduct;
            $order = $orderProduct->order;

            $obj = new \stdClass;
            $obj->orderId = $order->id;
            $obj->date = $order->date;
            $obj->client = $order->client ? $order->client->full_name : '';
            $obj->products = $maintenance->getProduct();
            $obj->status = $maintenance->getStatus();
            $obj->tecnician = $maintenance->tecnician ? $maintenance->tecnician->name : '';

            $ordersArr[] = $obj;
        }

        if($is_pdf > 0){
            $company_name = Configuration::where('key', 'company_name')->first()->value;
            $address = Configuration::where('key', 'address')->first()->value;
            $cellphone = Configuration::where('key', 'cellphone')->first()->value;
            $email = Configuration::where('key', 'email')->first()->value;

            $client = Client::find($clientId);
            $tecnician = User::find($tecnicianId);

            $pdf = PDF::loadView('reports.pdf_layouts.maintenance', [
                'orders' => $ordersArr,
                'client' => $client ? $client->full_name : 'Todos os Clientes',
                'tecnician' => $tecnician ? $tecnician->name : 'Todos os Técnicos',
                'status' => $status != '0' ? $this->parseStatus($status) : 'Todas as Situações',
                'startDate' => $startDate ? Carbon::parse($startDate)->format('d/m/Y') : '',
                'endDate' => $startDate ? Carbon::parse($endDate)->format('d/m/Y') : '',
                'company_name' => $company_name,
                'address' => $address,
                'cellphone' => $cellphone,
                'email' => $email,
            ]);

            return $pdf->stream();
        }

        return $ordersArr;
    }

    public function parseStatus($status){
        $allStatus = [
            'waiting_approval' => 'Aguardando Aprovação do Cliente', 
            'approved' => 'Aprovado pelo Cliente', 
            'waiting_stock' => 'Aguardando Peça', 
            'maintenance' => 'Em Manutenção', 
            'no_maintenance' => 'Sem Concerto',
            'fixed' => 'Finalizado/Consertado', 
            'finished' => 'Enviado para o Caixa',
        ];

        return $allStatus[$status] ?? 'Indeterminado';
    }
}
