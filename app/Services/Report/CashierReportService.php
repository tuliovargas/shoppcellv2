<?php

namespace App\Services\Report;

use App\Models\CashierInfo;
use App\Models\User;
use App\Services\Utilities\Util;
use Carbon\Carbon;
use PDF;
use App\Models\Configuration;

class CashierReportService
{
    /**
     * @var CashierInfo
     */
    private $cashierInfo;

    /**
     * IndexCashierInfoService constructor.
     * @param CashierInfo $cashierInfo
     */
    public function __construct(CashierInfo $cashierInfo)
    {
        $this->cashierInfo = $cashierInfo;
    }

    /**
     * @return mixed
     */
    public function run($data)
    {
        $is_pdf = $data['is_pdf'] ?? 0;
        $userId = $data['user'] ?? null;
        $status = $data['status'] ?? null;
        $startDate = $data['start_date'] ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $data['end_date'] ?? Carbon::today()->toDateString();

        $cashiers = $this->cashierInfo
            ->where(function ($query) use ($status) {
                if($status == 'opened'){
                    $query->whereNull('close_time');
                } elseif($status == 'closed'){
                    $query->whereNotNull('close_time');
                }
            })
            ->where(function ($query) use ($userId) {
                if($userId && $userId > 0){
                    $query->where('user_id', $userId);
                }
            })
            ->when($startDate != null, function ($query) use ($startDate){
                return $query->where('created_at', '>=', $startDate . ' 00:00:00');
            })
            ->when($endDate != null, function ($query) use ($endDate){
                return $query->where('created_at', '<=', $endDate . ' 23:59:59');
            })
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->get();

        $cashiersArr = [];

        foreach($cashiers as $cashier){
            $obj = new \stdClass;
            $obj->abertura = Carbon::parse($cashier->created_at)->format('d/m/Y H:i:s');
            $obj->fechamento = $cashier->close_time ? Carbon::parse($cashier->close_time)->format('d/m/Y H:i:s') : '---';
            $obj->usuario = $cashier->user->name;
            $obj->troco = Util::doubleToString($cashier->charge);
            $obj->status = $cashier->close_time ? 'Fechado' : 'Aberto';
            $obj->id = $cashier->id;

            $cashiersArr[] = $obj;
        }

        if($is_pdf > 0){
            $company_name = Configuration::where('key', 'company_name')->first()->value;
            $address = Configuration::where('key', 'address')->first()->value;
            $cellphone = Configuration::where('key', 'cellphone')->first()->value;
            $email = Configuration::where('key', 'email')->first()->value;

            $user = User::find($userId);

            $pdf = PDF::loadView('reports.pdf_layouts.cashier', [
                'cashiers' => $cashiersArr,
                'user' => $user ? $user->name : 'Todos os Usuários',
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

        return $cashiersArr;
    }

    public function parseStatus($status){
        $allStatus = [
            'opened' => 'Aberto',
            'closed' => 'Fechado'
        ];

        return $allStatus[$status] ?? 'Indeterminado';
    }
}
