<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Models\Expense;
use App\Services\Expense\DeleteExpenseService;
use App\Services\Expense\IndexExpenseService;
use App\Services\Expense\StoreExpenseService;
use App\Services\Expense\UpdateExpenseService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseController extends Controller
{
    /**
     * @var DeleteExpenseService
     */
    private $deleteExpenseService;

    /**
     * @var UpdateExpenseService
     */
    private $updateExpenseService;

    /**
     * @var StoreExpenseService
     */
    private $storeExpenseService;

    /**
     * @var IndexExpenseService
     */
    private $indexExpenseService;

    /**
     * ExpenseController constructor.
     * @param IndexExpenseService $indexExpenseService
     * @param StoreExpenseService $storeExpenseService
     * @param UpdateExpenseService $updateExpenseService
     * @param DeleteExpenseService $deleteExpenseService
     */
    public function __construct(
        IndexExpenseService $indexExpenseService,
        StoreExpenseService $storeExpenseService,
        UpdateExpenseService $updateExpenseService,
        DeleteExpenseService $deleteExpenseService
    ) {
        $this->indexExpenseService = $indexExpenseService;
        $this->storeExpenseService = $storeExpenseService;
        $this->updateExpenseService = $updateExpenseService;
        $this->deleteExpenseService = $deleteExpenseService;
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

        $expenses = $this->indexExpenseService->run($request);

        if ($request->expectsJson()) {
            return response($expenses);
        }

        return view('expenses.index')
            ->with('expenses', $expenses)
            ->with('search', $search);
    }

    /**
     * @param StoreExpenseRequest $storeExpenseRequest
     * @return RedirectResponse
     */
    public function store(StoreExpenseRequest $storeExpenseRequest)
    {
        $data = $storeExpenseRequest->validated();
        $expense = $this->storeExpenseService->run($data);

        if ($storeExpenseRequest->expectsJson()) {
            return response($expense);
        }

        return redirect()->route('expenses.index');
    }

    /**
     * @param UpdateExpenseRequest $updateExpenseRequest
     * @param Expense $expense
     * @return RedirectResponse
     */
    public function update(UpdateExpenseRequest $updateExpenseRequest, Expense $expense)
    {
        $data = $updateExpenseRequest->validated();
        $expense = $this->updateExpenseService->run($data, $expense);

        if ($updateExpenseRequest->expectsJson()) {
            return response($expense->refresh());
        }

        return redirect()->route('expense.index');
    }

    /**
     * @param Expense $expense
     * @return Application|ResponseFactory|Response
     */
    public function destroy(Expense $expense)
    {
        $this->deleteExpenseService->run($expense);
        return response(route('expense.index'));
    }

    public function delete(Expense $expense)
    {
    }
}
