<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Services\Report\CashierReportService;
use App\Services\Report\CommissionReportService;
use App\Services\Report\MaintenanceReportService;
use App\Services\Report\SalesReportService;
use App\Services\Report\ExpensesReportService;
use App\Services\Report\InventoryReportService;
use App\Services\Report\RequestsReportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Client;
use App\Models\ExpenseType;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * @var CashierReportService
     */
    private $cashierReportService;

    /**
     * @var CommissionReportService
     */
    private $commissionReportService;

    /**
     * @var MaintenanceReportService
     */
    private $maintenanceReportService;

    /**
     * @var SalesReportService
     */
    private $salesReportService;

    /**
     * @var ExpensesReportService
     */
    private $expensesReportService;

    /**
     * @var InventoryReportService
     */
    private $inventoryReportService;

    /**
     * @var RequestsReportService
     */
    private $requestsReportService;

    /**
     * ReportController constructor.
     * @param CashierReportService $cashierReportService
     * @param CommissionReportService $commissionReportService
     * @param MaintenanceReportService $maintenanceReportService
     * @param SalesReportService $salesReportService
     * @param ExpensesReportService $expensesReportService
     * @param InventoryReportService $inventoryReportService
     * @param RequestsReportService $requestsReportService
     */
    public function __construct(
        CashierReportService $cashierReportService,
        CommissionReportService $commissionReportService,
        MaintenanceReportService $maintenanceReportService,
        SalesReportService $salesReportService,
        ExpensesReportService $expensesReportService,
        InventoryReportService $inventoryReportService,
        RequestsReportService $requestsReportService
    )
    {
        $this->cashierReportService = $cashierReportService;
        $this->commissionReportService = $commissionReportService;
        $this->maintenanceReportService = $maintenanceReportService;
        $this->salesReportService = $salesReportService;
        $this->expensesReportService = $expensesReportService;
        $this->inventoryReportService = $inventoryReportService;
        $this->requestsReportService = $requestsReportService;
    }

    /**
     * @return Application|Factory|View
     */
    public function cashier(Request $request)
    {
        $data = $request->all();
        $is_pdf = $request->is_pdf ?? 0;
        $userId = $request->user ?? null;
        $status = $request->status ?? null;
        $startDate = $request->start_date ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->toDateString();
        $cashiers = [];
        $cashiers = $this->cashierReportService->run($data);

        if($is_pdf > 0){
            return $cashiers;
        }
        
        $users = User::whereHas('roles', function(Builder $query) {
            $query->whereIn('name', ['vendedor', 'técnico', 'admin', 'caixa']);
        })
        ->orderBy('name')
        ->get();

        return view('reports.cashier')
            ->with('cashiers', $cashiers)
            ->with('users', $users)
            ->with('status', $status)
            ->with('userId', $userId)
            ->with('startDate', $startDate)
            ->with('endDate', $endDate);
    }

    /**
     * @return Application|Factory|View
     */
    public function commission(Request $request)
    {
        $data = $request->all();
        $userId = $request->user ?? null;
        $startDate = $request->start_date ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->toDateString();
        $cashier = [];

        if($request->annualReport && $request->type == 'vue'){
            return $this->commissionReportService->run($data);
        }

        if($userId != null){
            $cashier = $this->commissionReportService->run($data);
        }
        
        $users = User::whereHas('roles', function(Builder $query) {
            $query->whereIn('name', ['vendedor', 'técnico', 'admin']);
        })
        ->orderBy('name')
        ->get();

        return view('reports.commission')
            ->with('cashier', $cashier)
            ->with('users', $users)
            ->with('userId', $userId)
            ->with('startDate', $startDate)
            ->with('endDate', $endDate);
    }

    /**
     * @return Application|Factory|View
     */
    public function maintenance(Request $request)
    {
        $data = $request->all();
        $is_pdf = $request->is_pdf ?? 0;
        $clientId = $request->client ?? null;
        $tecnicianId = $request->tecnician ?? null;
        $status = $request->status ?? null;
        $startDate = $request->start_date ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->toDateString();
        $orders = [];

        $orders = $this->maintenanceReportService->run($data);

        if($is_pdf > 0){
            return $orders;
        }
        
        $tecnicians = User::whereHas('roles', function(Builder $query) {
            $query->whereIn('name', ['técnico']);
        })
        ->orderBy('name')
        ->get();

        $clients = Client::whereHas('orders')
            ->orderBy('full_name')
            ->get();

        return view('reports.maintenance')
            ->with('orders', $orders)
            ->with('tecnicians', $tecnicians)
            ->with('clients', $clients)
            ->with('clientId', $clientId)
            ->with('tecnicianId', $tecnicianId)
            ->with('status', $status)
            ->with('startDate', $startDate)
            ->with('endDate', $endDate);
    }

    /**
     * @return Application|Factory|View
     */
    public function sales(Request $request)
    {
        $data = $request->all();
        $is_pdf = $request->is_pdf ?? 0;
        $clientId = $request->client ?? null;
        $sellerId = $request->seller ?? null;
        $status = $request->status ?? null;
        $startDate = $request->start_date ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->toDateString();
        $orders = [];

        
        if($request->annualReport && $request->type == 'vue'){
            return $this->salesReportService->run($data);
        }
        $orders = $this->salesReportService->run($data);

        if($is_pdf > 0){
            return $orders;
        }

        if($is_pdf > 0){
            return $orders;
        }
        
        $users = User::whereHas('roles', function(Builder $query) {
            $query->whereIn('name', ['vendedor']);
        })
        ->orderBy('name')
        ->get();

        $clients = Client::whereHas('orders')
            ->orderBy('full_name')
            ->get();

        return view('reports.sales')
            ->with('orders', $orders)
            ->with('users', $users)
            ->with('clients', $clients)
            ->with('clientId', $clientId)
            ->with('sellerId', $sellerId)
            ->with('status', $status)
            ->with('startDate', $startDate)
            ->with('endDate', $endDate);
    }

    /**
     * @return Application|Factory|View
     */
    public function expenses(Request $request)
    {
        $data = $request->all();
        $is_pdf = $request->is_pdf ?? 0;
        $tipoId = $request->tipo ?? null;
        $metodoId = $request->metodo ?? null;
        $startLancDate = $request->start_lanc_date ?? '';
        $endLancDate = $request->end_lanc_date ?? '';
        $startPgtoDate = $request->start_pgto_date ?? '';
        $endPgtoDate = $request->end_pgto_date ?? '';

        if(!$request->has('tipo')){
            $startPgtoDate = Carbon::today()->subMonth()->toDateString();
            $endPgtoDate = Carbon::today()->toDateString();
        }

        $expenses = $this->expensesReportService->run($data);
        if($request->type == 'vue'){
            return $expenses;
        }

        if($is_pdf > 0){
            return $expenses;
        }

        $tipos = ExpenseType::orderBy('name')->get();
        $metodos = PaymentMethod::orderBy('name')->get();
        
        return view('reports.expenses')
            ->with('expenses', $expenses)
            ->with('startLancDate', $startLancDate)
            ->with('endLancDate', $endLancDate)
            ->with('startPgtoDate', $startPgtoDate)
            ->with('endPgtoDate', $endPgtoDate)
            ->with('tipoId', $tipoId)
            ->with('metodoId', $metodoId)
            ->with('tipos', $tipos)
            ->with('metodos', $metodos);
    }

    /**
     * @return Application|Factory|View
     */
    public function inventory(Request $request)
    {
        $data = $request->all();
        $is_pdf = $request->is_pdf ?? 0;
        $active = $request->active ?? '1';
        $min_ini = $request->min_ini ?? '';
        $min_fim = $request->min_fim ?? '';
        $atual_ini = $request->atual_ini ?? '';
        $atual_fim = $request->atual_fim ?? '';

        if(!$request->has('min_ini')){
            $atual_ini = 0;
            $atual_fim = 0;
        }

        $products = $this->inventoryReportService->run($data);

        if($is_pdf > 0){
            return $products;
        }
        
        return view('reports.inventory')
            ->with('active', $active)
            ->with('min_ini', $min_ini)
            ->with('min_fim', $min_fim)
            ->with('atual_ini', $atual_ini)
            ->with('atual_fim', $atual_fim)
            ->with('products', $products);
    }

    /**
     * @return Application|Factory|View
     */
    public function requests(Request $request)
    {
        $data = $request->all();
        $is_pdf = $request->is_pdf ?? 0;
        $clientId = $request->client ?? null;
        $productId = $request->product ?? null;
        $startDate = $request->start_date ?? Carbon::today()->subMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->toDateString();
        $requests = [];

        $requests = $this->requestsReportService->run($data);

        if($is_pdf > 0){
            return $requests;
        }
        
        $products = Product::whereHas('orders', function(Builder $query) {
                $query->where('status', ['waiting_product']);
            })
            ->where('quantity_in_stock', '<=', 0)
            ->orderBy('name')
            ->get();

        $clients = Client::whereHas('orders', function(Builder $query) {
                $query->where('status', ['waiting_product']);
            })
            ->orderBy('full_name')
            ->get();

        return view('reports.requests')
            ->with('requests', $requests)
            ->with('products', $products)
            ->with('clients', $clients)
            ->with('clientId', $clientId)
            ->with('productId', $productId)
            ->with('startDate', $startDate)
            ->with('endDate', $endDate);
    }

    public function cost(Request $request)
    {
        $data = $request->all();

        $sqlCosts = Helpers::unionReport('order_products', '', 'created_at', $data['month'], $data['year'], 'SUM(cost * amount)');

        $totals = DB::select(DB::raw($sqlCosts));

        return [
            'totals' => array_column($totals, 'total')
        ];

        OrderProduct::query()
            ->where(DB::raw('MONTH(created_at)'), '=', $data['month'])
            ->where(DB::raw('YEAR(created_at)'), '=', $data['year'])
            ->get();
    }

    public function period()
    {
        $period = DB::select(
            DB::raw("select
                    min(min) as min,
                    max(max) as max
                from
                    (
                    select
                        min(year(created_at)) as min,
                        max(year(created_at)) as max
                    from
                        cashiers c
                union all
                    select
                        min(year(created_at)) as min,
                        max(year(created_at)) as max
                    from
                        expenses e
                union all
                    select
                        min(year(created_at)) as min,
                        max(year(created_at)) as max
                    from
                        order_products op
                union all
                    select
                        min(year(created_at)) as min,
                        max(year(created_at)) as max
                    from
                        orders od) period")
        );
        return response()->json(
            $period[0]
        );
    }
}
