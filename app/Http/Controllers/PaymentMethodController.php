<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Services\PaymentMethod\DeletePaymentMethodService;
use App\Services\PaymentMethod\IndexPaymentMethodService;
use App\Services\PaymentMethod\StorePaymentMethodService;
use App\Services\PaymentMethod\UpdatePaymentMethodService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentMethodController extends Controller
{
    /**
     * @var DeletePaymentMethodService
     */
    private $deletePaymentMethodService;

    /**
     * @var UpdatePaymentMethodService
     */
    private $updatePaymentMethodService;

    /**
     * @var StorePaymentMethodService
     */
    private $storePaymentMethodService;

    /**
     * @var IndexPaymentMethodService
     */
    private $indexPaymentMethodService;

    /**
     * PaymentMethodController constructor.
     * @param IndexPaymentMethodService $indexPaymentMethodService
     * @param StorePaymentMethodService $storePaymentMethodService
     * @param UpdatePaymentMethodService $updatePaymentMethodService
     * @param DeletePaymentMethodService $deletePaymentMethodService
     */
    public function __construct(
        IndexPaymentMethodService $indexPaymentMethodService,
        StorePaymentMethodService $storePaymentMethodService,
        UpdatePaymentMethodService $updatePaymentMethodService,
        DeletePaymentMethodService $deletePaymentMethodService
    ) {
        $this->indexPaymentMethodService = $indexPaymentMethodService;
        $this->storePaymentMethodService = $storePaymentMethodService;
        $this->updatePaymentMethodService = $updatePaymentMethodService;
        $this->deletePaymentMethodService = $deletePaymentMethodService;
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

        $paymentMethods = $this->indexPaymentMethodService->run($request);

        if ($request->expectsJson()) {
            return response()->json($paymentMethods);
        }

        return view('payment-methods.index')
            ->with('paymentMethods', $paymentMethods)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('payment-methods.create');
    }

    /**
     * @param StorePaymentMethodRequest $storePaymentMethodRequest
     * @return RedirectResponse
     */
    public function store(StorePaymentMethodRequest $storePaymentMethodRequest)
    {
        $data = $storePaymentMethodRequest->validated();
        $this->storePaymentMethodService->run($data);
        return redirect()->route('payment-methods.index');
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return Application|Factory|View
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.edit')
            ->with('paymentMethod', $paymentMethod);
    }

    /**
     * @param UpdatePaymentMethodRequest $updatePaymentMethodRequest
     * @param PaymentMethod $paymentMethod
     * @return RedirectResponse
     */
    public function update(UpdatePaymentMethodRequest $updatePaymentMethodRequest, PaymentMethod $paymentMethod)
    {
        $data = $updatePaymentMethodRequest->validated();
        $this->updatePaymentMethodService->run($data, $paymentMethod);
        return redirect()->route('payment-methods.index');
    }

    /**
     * @param Product $product
     * @return Application|Factory|View
     */
    public function delete(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.delete', compact('paymentMethod'));
    }

    /**
     * @param PaymentMethod $expenseType
     * @return Application|ResponseFactory|Response
     */
    public function destroy(PaymentMethod $expenseType)
    {
        $this->deletePaymentMethodService->run($expenseType);
        return response(route('payment-methods.index'));
    }
}
