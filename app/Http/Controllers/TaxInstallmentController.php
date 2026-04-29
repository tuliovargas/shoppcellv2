<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaxInstallment\StoreTaxInstallmentRequest;
use App\Http\Requests\TaxInstallment\UpdateTaxInstallmentRequest;
use App\Models\TaxInstallment;
use App\Services\TaxInstallment\DeleteTaxInstallmentService;
use App\Services\TaxInstallment\IndexTaxInstallmentService;
use App\Services\TaxInstallment\StoreTaxInstallmentService;
use App\Services\TaxInstallment\UpdateTaxInstallmentService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaxInstallmentController extends Controller
{
    /**
     * @var DeleteTaxInstallmentService
     */
    private $deleteTaxInstallmentService;

    /**
     * @var UpdateTaxInstallmentService
     */
    private $updateTaxInstallmentService;

    /**
     * @var StoreTaxInstallmentService
     */
    private $storeTaxInstallmentService;

    /**
     * @var IndexTaxInstallmentService
     */
    private $indexTaxInstallmentService;

    /**
     * TaxInstallmentController constructor.
     * @param IndexTaxInstallmentService $indexTaxInstallmentService
     * @param StoreTaxInstallmentService $storeTaxInstallmentService
     * @param UpdateTaxInstallmentService $updateTaxInstallmentService
     * @param DeleteTaxInstallmentService $deleteTaxInstallmentService
     */
    public function __construct(
        IndexTaxInstallmentService $indexTaxInstallmentService,
        StoreTaxInstallmentService $storeTaxInstallmentService,
        UpdateTaxInstallmentService $updateTaxInstallmentService,
        DeleteTaxInstallmentService $deleteTaxInstallmentService)
    {
        $this->indexTaxInstallmentService = $indexTaxInstallmentService;
        $this->storeTaxInstallmentService = $storeTaxInstallmentService;
        $this->updateTaxInstallmentService = $updateTaxInstallmentService;
        $this->deleteTaxInstallmentService = $deleteTaxInstallmentService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Factory|View|Response
     */
    public function index(Request $request)
    {
        $taxInstallments = $this->indexTaxInstallmentService->run();

        if ($request->expectsJson()) {
            return response($taxInstallments);
        }

        return view('tax-installments.index')
            ->with('taxInstallments', $taxInstallments);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('tax-installments.create');
    }

    /**
     * @param StoreTaxInstallmentRequest $storeTaxInstallmentRequest
     * @return RedirectResponse
     */
    public function store(StoreTaxInstallmentRequest $storeTaxInstallmentRequest)
    {
        $data = $storeTaxInstallmentRequest->validated();
        $this->storeTaxInstallmentService->run($data);
        return redirect()->route('tax-installments.index');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function edit(Request $request)
    {
        $taxInstallments = $this->indexTaxInstallmentService->run();

        if ($request->expectsJson()) {
            return response()->json($taxInstallments);
        }

        $taxInstallments = TaxInstallment::all();

        return view('tax-installments.index')
            ->with('taxInstallments', $taxInstallments);
    }

    /**
     * @param UpdateTaxInstallmentRequest $updateTaxInstallmentRequest
     * @return RedirectResponse
     */
    public function update(UpdateTaxInstallmentRequest $updateTaxInstallmentRequest)
    {
        $data = $updateTaxInstallmentRequest->tax_installments;
        $this->updateTaxInstallmentService->run($data);
        return redirect()->route('configurations.tax-installments.edit');
    }

    /**
     * @param TaxInstallment $taxInstallment
     * @return Application|ResponseFactory|Response
     */
    public function destroy(TaxInstallment $taxInstallment)
    {
        $this->deleteTaxInstallmentService->run($taxInstallment);
        return response(route('tax-installments.index'));
    }
}
