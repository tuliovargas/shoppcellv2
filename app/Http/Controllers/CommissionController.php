<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Report\CommissionReportService;

class CommissionController extends Controller
{
    /**
     * @var CommissionReportService
     */
    private $commissionReportService;

    /**
     * CommissionController constructor.
     * @param CommissionReportService $commissionReportService
     */
    public function __construct(
        CommissionReportService $commissionReportService
    )
    {
        $this->commissionReportService = $commissionReportService;
    }

    /**
     * Show the application dashboard.
     *
     * @return Application|Factory|View
     */
    public function process(Request $request)
    {
        $data = $request->all();

        return $this->commissionReportService->process($data);
    }
}
