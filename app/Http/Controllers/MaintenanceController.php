<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceInfo;
use App\Models\OrderProduct;
use App\Services\Maintenance\IndexMaintenanceService;
use App\Services\Maintenance\UpdateMaintenanceService;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * @var IndexMaintenanceService
     */
    private $indexMaintenanceService;

    /**
     * @var UpdateMaintenanceService
     */
    private $updateMaintenanceService;

    /**
     * MaintenanceController constructor.
     * @param IndexMaintenanceService $indexMaintenanceService,
     */
    public function __construct(
        IndexMaintenanceService $indexMaintenanceService,
        UpdateMaintenanceService $updateMaintenanceService
    ) {
        $this->indexMaintenanceService = $indexMaintenanceService;
        $this->updateMaintenanceService = $updateMaintenanceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $orders = $this->indexMaintenanceService->run($request);
            return response()->json($orders);
        }

        return view('maintenance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @var  OrderProduct  $orderProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $maintenanceInfo = MaintenanceInfo::findOrFail($id);
        $orderProduct = $maintenanceInfo->orderProduct;
        $order = $this->updateMaintenanceService->run($data, $orderProduct);
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getOverdue()
    {
        return response([
            'total' => MaintenanceInfo::query()
                ->whereNotNull('due_date')
                ->where('due_date', '<', 'CURDATE()')
                ->count()
        ]);
    }
}
