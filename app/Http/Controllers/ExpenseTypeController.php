<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseType\StoreExpenseTypeRequest;
use App\Http\Requests\ExpenseType\UpdateExpenseTypeRequest;
use App\Models\ExpenseType;
use App\Services\ExpenseType\DeleteExpenseTypeService;
use App\Services\ExpenseType\IndexExpenseTypeService;
use App\Services\ExpenseType\StoreExpenseTypeService;
use App\Services\ExpenseType\UpdateExpenseTypeService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseTypeController extends Controller
{
    /**
     * @var DeleteExpenseTypeService
     */
    private $deleteExpenseTypeService;

    /**
     * @var UpdateExpenseTypeService
     */
    private $updateExpenseTypeService;

    /**
     * @var StoreExpenseTypeService
     */
    private $storeExpenseTypeService;

    /**
     * @var IndexExpenseTypeService
     */
    private $indexExpenseTypeService;

    /**
     * ExpenseTypeController constructor.
     * @param IndexExpenseTypeService $indexExpenseTypeService
     * @param StoreExpenseTypeService $storeExpenseTypeService
     * @param UpdateExpenseTypeService $updateExpenseTypeService
     * @param DeleteExpenseTypeService $deleteExpenseTypeService
     */
    public function __construct(
        IndexExpenseTypeService $indexExpenseTypeService,
        StoreExpenseTypeService $storeExpenseTypeService,
        UpdateExpenseTypeService $updateExpenseTypeService,
        DeleteExpenseTypeService $deleteExpenseTypeService)
    {
        $this->indexExpenseTypeService = $indexExpenseTypeService;
        $this->storeExpenseTypeService = $storeExpenseTypeService;
        $this->updateExpenseTypeService = $updateExpenseTypeService;
        $this->deleteExpenseTypeService = $deleteExpenseTypeService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Factory|View|Response
     */
    public function index(Request $request)
    {
        $search = null;
        if ($request->search) {
            $search = $request->search;
        }

        $expenseTypes = $this->indexExpenseTypeService->run($request);

        if ($request->expectsJson()) {
            return response($expenseTypes);
        }

        return view('expense-types.index')
            ->with('expenseTypes', $expenseTypes)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('expense-types.create');
    }

    /**
     * @param StoreExpenseTypeRequest $storeExpenseTypeRequest
     * @return RedirectResponse
     */
    public function store(StoreExpenseTypeRequest $storeExpenseTypeRequest)
    {
        $data = $storeExpenseTypeRequest->validated();
        $this->storeExpenseTypeService->run($data);
        return redirect()->route('expense-types.index');
    }

    /**
     * @param ExpenseType $expenseType
     * @return Application|Factory|View
     */
    public function edit(ExpenseType $expenseType)
    {
        return view('expense-types.edit')
            ->with('expenseType', $expenseType);
    }

    /**
     * @param UpdateExpenseTypeRequest $updateExpenseTypeRequest
     * @param ExpenseType $expenseType
     * @return RedirectResponse
     */
    public function update(UpdateExpenseTypeRequest $updateExpenseTypeRequest, ExpenseType $expenseType)
    {
        $data = $updateExpenseTypeRequest->validated();
        $this->updateExpenseTypeService->run($data, $expenseType);
        return redirect()->route('expense-types.index');
    }

    /**
     * @param ExpenseType $expenseType
     * @return Application|ResponseFactory|Response
     */
    public function destroy(ExpenseType $expenseType)
    {
        $this->deleteExpenseTypeService->run($expenseType);
        return response(route('expense-types.index'));
    }
}
