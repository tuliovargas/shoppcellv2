<?php

namespace App\Services\Report;

use App\Helpers;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\ExpenseType;
use Carbon\Carbon;
use App\Services\Utilities\Util;
use PDF;
use App\Models\Configuration;
use Illuminate\Support\Facades\DB;

class ExpensesReportService
{
    /**
     * @var Expense
     */
    private $expense;

    /**
     * IndexExpenseService constructor.
     * @param Expense $expense
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
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
        $tipoId = $data['tipo'] ?? null;
        $metodoId = $data['metodo'] ?? null;
        $startLancDate = $data['start_lanc_date'] ?? null;
        $endLancDate = $data['end_lanc_date'] ?? null;
        $startPgtoDate = $data['start_pgto_date'] ?? null;
        $endPgtoDate = $data['end_pgto_date'] ?? null;

        if(!array_key_exists('tipo', $data)){
            $startPgtoDate = Carbon::today()->subMonth()->toDateString();
            $endPgtoDate = Carbon::today()->toDateString();
        }

        $expenses = $this->expense
            ->where(function ($query) use ($tipoId) {
                if($tipoId && $tipoId > 0){
                    $query->where('expense_type_id', $tipoId);
                }
            })
            ->where(function ($query) use ($metodoId) {
                if($metodoId && $metodoId > 0){
                    $query->where('payment_method_id', $metodoId);
                }
            })
            ->when($startLancDate != null, function ($query) use ($startLancDate){
                return $query->where('created_at', '>=', $startLancDate . ' 00:00:00');
            })
            ->when($endLancDate != null, function ($query) use ($endLancDate){
                return $query->where('created_at', '<=', $endLancDate . ' 23:59:59');
            })
            ->when($startPgtoDate != null, function ($query) use ($startPgtoDate){
                return $query->where('payday', '>=', $startPgtoDate . ' 00:00:00');
            })
            ->when($endPgtoDate != null, function ($query) use ($endPgtoDate){
                return $query->where('payday', '<=', $endPgtoDate . ' 23:59:59');
            })
            ->orderBy('payday', 'desc')
            ->get();

        if($is_pdf > 0){
            $company_name = Configuration::where('key', 'company_name')->first()->value;
            $address = Configuration::where('key', 'address')->first()->value;
            $cellphone = Configuration::where('key', 'cellphone')->first()->value;
            $email = Configuration::where('key', 'email')->first()->value;

            $tipo = ExpenseType::find($tipoId);
            $metodo = PaymentMethod::find($metodoId);

            $total = $expenses->sum('value');

            $pdf = PDF::loadView('reports.pdf_layouts.expenses', [
                'expenses' => $expenses,
                'tipo' => $tipo ? $tipo->name : 'Todos os Tipos',
                'metodo' =>$metodo ? $metodo->name : 'Todos os Métodos',
                'startLancDate' => $startLancDate ? Carbon::parse($startLancDate)->format('d/m/Y') : '',
                'endLancDate' => $endLancDate ? Carbon::parse($endLancDate)->format('d/m/Y') : '',
                'startPgtoDate' => $startPgtoDate ? Carbon::parse($startPgtoDate)->format('d/m/Y') : '',
                'endPgtoDate' => $endPgtoDate ? Carbon::parse($endPgtoDate)->format('d/m/Y') : '',
                'total' => Util::doubleToString($total),
                'company_name' => $company_name,
                'address' => $address,
                'cellphone' => $cellphone,
                'email' => $email,
            ]);

            return $pdf->stream();
        }

        return $expenses;
    }
    private static function rawReportSql($month, $year)
    {
        /* Pega o total de gastos da tabela expenses */
        $sqlExpenses = Helpers::unionReport('expenses', 'value', 'created_at', $month, $year);

        $totals = DB::select(DB::raw($sqlExpenses));
        return [
            'totals' => array_column($totals, 'total')
        ];
    }
}
