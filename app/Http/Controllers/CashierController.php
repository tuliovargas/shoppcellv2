<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cashier\StoreCashierRequest;
use App\Models\PaymentMethod;
use App\Models\Supplier;
use App\Services\Cashier\IndexCashierService;
use App\Services\Cashier\StoreCashierService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CashierController extends Controller
{
    /**
     * @var StoreCashierService
     */
    private $storeCashierService;

    /**
     * @var IndexCashierService
     */
    private $indexCashierService;

    /**
     * CashierController constructor.
     * @param IndexCashierService $indexCashierService
     * @param StoreCashierService $storeCashierService
     */
    public function __construct(
        IndexCashierService $indexCashierService,
        StoreCashierService $storeCashierService
    )
    {
        $this->indexCashierService = $indexCashierService;
        $this->storeCashierService = $storeCashierService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Factory|View|Response
     */
    public function index(Request $request)
    {
        $cashier = $this->indexCashierService->run();
        $suppliers = Supplier::all(); 
        $paymentMethod = PaymentMethod::all(); 
        if ($request->expectsJson()) {
            return response($cashier);
        }

        return view('cashier.create', compact('suppliers'), compact('paymentMethod'))
            ->with('cashier', $cashier);
    }

    /**
     * @param StoreCashierRequest $storeCashierRequest
     * @return Application|ResponseFactory|Response
     */
    public function store(StoreCashierRequest $storeCashierRequest)
    {
        $data = $storeCashierRequest->validated();
        $cashier = $this->storeCashierService->run($data);
        return response($cashier);
    }
}
