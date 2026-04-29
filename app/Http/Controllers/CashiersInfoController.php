<?php

namespace App\Http\Controllers;

use App\Services\CashierInfo\ChangeCashierInfoService;
use App\Services\CashierInfo\CloseCashierInfoService;
use App\Services\CashierInfo\IndexCashierInfoService;
use App\Services\CashierInfo\StoreCashierInfoService;
use App\Services\CashierInfo\HistoryCashierInfoService;
use App\Models\CashierInfo;
use Illuminate\Http\Request;

class CashiersInfoController extends Controller
{
    /**
     * @var StoreCashierInfoService
     */
    private $storeCashierInfoService;

    /**
     * @var IndexCashierInfoService
     */
    private $indexCashierInfoService;

    /**
     * @var ChangeCashierInfoService
     */
    private $changeCashierInfoService;

    /**
     * @var CloseCashierInfoService
     */
    private $closeCashierInfoService;

    /**
     * @var HistoryCashierInfoService
     */
    private $historyCashierInfoService;

    /**
     * CashierController constructor.
     * @param IndexCashierInfoService $indexCashierInfoService
     * @param StoreCashierInfoService $storeCashierInfoService
     * @param ChangeCashierInfoService $changeCashierInfoService
     * @param CloseCashierInfoService $closeCashierInfoService
     * @param HistoryCashierInfoService $historyCashierInfoService
     */
    public function __construct(
        IndexCashierInfoService $indexCashierInfoService,
        StoreCashierInfoService $storeCashierInfoService,
        ChangeCashierInfoService $changeCashierInfoService,
        CloseCashierInfoService $closeCashierInfoService,
        HistoryCashierInfoService $historyCashierInfoService
    ) {
        $this->indexCashierInfoService = $indexCashierInfoService;
        $this->storeCashierInfoService = $storeCashierInfoService;
        $this->changeCashierInfoService = $changeCashierInfoService;
        $this->closeCashierInfoService = $closeCashierInfoService;
        $this->historyCashierInfoService = $historyCashierInfoService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cashier = $this->indexCashierInfoService->run($request);

        return response()->json($cashier);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cashier = $this->storeCashierInfoService->run($request->all());

        return response()->json($cashier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CashierInfo $cashier)
    {
        $cashier = $this->changeCashierInfoService->run($request->all(), $cashier);
        if ($cashier) {
            return response()->json($cashier);
        } else {
            return response()->json([
                'errors' => [
                    'Não existe nenhum caixa aberto',
                ]
            ])->status(404);
        }
    }

    /**
     * Close the last resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $cashier = $this->closeCashierInfoService->run($request->all());
        if ($cashier) {
            return response()->json($cashier);
        } else {
            return response()->json([
                'errors' => [
                    'Não existe nenhum caixa aberto',
                ]
            ])->status(404);
        }
    }

    /**
     * Get the lasts cashierInfo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request)
    {
        $cashier = $this->historyCashierInfoService->run($request->all());

        return response()->json($cashier);
    }
    
}
