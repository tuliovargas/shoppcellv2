<?php

namespace App\Http\Controllers;

use App\Http\Requests\Bank\StoreBankRequest;
use App\Http\Requests\Bank\UpdateBankRequest;
use App\Models\Bank;
use App\Services\Bank\DeleteBankService;
use App\Services\Bank\IndexBankService;
use App\Services\Bank\StoreBankService;
use App\Services\Bank\UpdateBankService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankController extends Controller
{
    /**
     * @var DeleteBankService
     */
    private $deleteBankService;

    /**
     * @var UpdateBankService
     */
    private $updateBankService;

    /**
     * @var StoreBankService
     */
    private $storeBankService;

    /**
     * @var IndexBankService
     */
    private $indexBankService;

    /**
     * BankController constructor.
     * @param IndexBankService $indexBankService
     * @param StoreBankService $storeBankService
     * @param UpdateBankService $updateBankService
     * @param DeleteBankService $deleteBankService
     */
    public function __construct(
        IndexBankService $indexBankService,
        StoreBankService $storeBankService,
        UpdateBankService $updateBankService,
        DeleteBankService $deleteBankService)
    {
        $this->indexBankService = $indexBankService;
        $this->storeBankService = $storeBankService;
        $this->updateBankService = $updateBankService;
        $this->deleteBankService = $deleteBankService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Factory|View|Response
     */
    public function index(Request $request)
    {
        $banks = $this->indexBankService->run();

        if ($request->expectsJson()) {
            return response($banks);
        }

        return view('banks.index')
            ->with('banks', $banks);
    }

    /**
     * @param StoreBankRequest $storeBankRequest
     * @return RedirectResponse
     */
    public function store(StoreBankRequest $storeBankRequest)
    {
        $data = $storeBankRequest->validated();
        $this->storeBankService->run($data);
        return redirect()->route('banks.index');
    }

    /**
     * @param UpdateBankRequest $updateBankRequest
     * @param Bank $bank
     * @return RedirectResponse
     */
    public function update(UpdateBankRequest $updateBankRequest, Bank $bank)
    {
        $data = $updateBankRequest->validated();
        $this->updateBankService->run($data, $bank);
        return redirect()->route('banks.index');
    }

    /**
     * @param Bank $bank
     * @return Application|ResponseFactory|Response
     */
    public function destroy(Bank $bank)
    {
        $this->deleteBankService->run($bank);
        return response(route('banks.index'));
    }
}
