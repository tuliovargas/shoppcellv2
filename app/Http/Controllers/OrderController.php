<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use App\Services\Order\DataTableOrderService;
use App\Services\Order\DeleteOrderService;
use App\Services\Order\IndexOrderService;
use App\Services\Order\StoreOrderService;
use App\Services\Order\UpdateOrderService;
use App\Services\Order\RefundOrderService;
use App\Services\Order\LastOrdersService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * @var DeleteOrderService
     */
    private $deleteOrderService;

    /**
     * @var UpdateOrderService
     */
    private $updateOrderService;

    /**
     * @var StoreOrderService
     */
    private $storeOrderService;

    /**
     * @var IndexOrderService
     */
    private $indexOrderService;

    /**
     * @var RefundOrderService
     */
    private $refundOrderService;

    /**
     * @var LastOrdersService
     */
    private $lastOrdersService;

    /**
     * OrderController constructor.
     * @param IndexOrderService $indexOrderService
     * @param StoreOrderService $storeOrderService
     * @param UpdateOrderService $updateOrderService
     * @param DeleteOrderService $deleteOrderService
     * @param RefundOrderService $refundOrderService
     * @param LastOrdersService $lastOrdersService
     */
    public function __construct(
        IndexOrderService $indexOrderService,
        StoreOrderService $storeOrderService,
        UpdateOrderService $updateOrderService,
        DeleteOrderService $deleteOrderService,
        RefundOrderService $refundOrderService,
        LastOrdersService $lastOrdersService
    ) {
        $this->indexOrderService = $indexOrderService;
        $this->storeOrderService = $storeOrderService;
        $this->updateOrderService = $updateOrderService;
        $this->deleteOrderService = $deleteOrderService;
        $this->refundOrderService = $refundOrderService;
        $this->lastOrdersService = $lastOrdersService;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request, DataTableOrderService $dataTableOrderService)
    {
        
        if ($request->ajax() && $request->get('type') !== 'vue') {
            return $dataTableOrderService->run($request->query('client_id'));
        }
        
        $search = null;
        if ($request->search) {
            $search = $request->search;
        }
        $orders = $this->indexOrderService->run($request);

        if ($request->expectsJson()) {
            return response($orders);
        }

        return view('orders.index')
            ->with('orders', $orders)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * @param StoreOrderRequest $storeOrderRequest
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $this->storeOrderService->run($data);

        if (!$request->json()) {
            return redirect()->route('orders.index');
        }
    }

    /**
     * @param Order $order
     * @return Application|Factory|View
     */
    public function show(Order $order)
    {   
        $order->load(
            'coupon', 
            'coupons', 
            'products', 
            'products.product', 
            'products.product.checklists', 
            'products.byProducts', 
            'products.byProducts.product', 
            'client', 
            'user', 
            'comments', 
            'seller', 
            'client.address', 
            'products.maintenance', 
            'products.maintenance.tecnician', 
            'payments',
            'payments.payment_method');

        return response()->json($order);
    }

    /**
     * @param Order $order
     * @return Application|Factory|View
     */
    public function edit(Order $order)
    {
        return view('orders.edit')
            ->with('order', $order);
    }

    /**
     * @param UpdateOrderRequest $updateOrderRequest
     * @param Order $order
     * @return RedirectResponse
     */
    public function update(UpdateOrderRequest $updateOrderRequest, Order $order)
    {
        $data = $updateOrderRequest->validated();
        $order = $this->updateOrderService->run($data, $order);

        return response()->json($order);
    }

    /**
     * @param Order $order
     * @return Application|ResponseFactory|Response
     */
    public function destroy(Order $order)
    {
        $this->deleteOrderService->run($order);
        return response(route('orders.index'));
    }


    /**
     * @param Order $order
     * @return Application|ResponseFactory|Response
     */
    public function refund(Request $request, Order $order)
    {
        $order = $this->refundOrderService->run($request->all(), $order);
        return response($order);
    }

    /**
     * @return Application|Factory|View
     */
    public function last(Request $request){
        $order = $this->lastOrdersService->run($request->all());
        return response($order);
    }
}
