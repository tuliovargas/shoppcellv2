<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stock\StoreStockRequest;
use App\Models\Product;
use App\Services\Stock\IndexStockService;
use App\Services\Stock\StoreStockService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class StockController extends Controller
{
    /**
     * @var IndexStockService
     */
    private $indexStockService;

    /**
     * @var StoreStockService
     */
    private $storeStockService;

    /**
     * StockController constructor.
     * @param IndexStockService $indexStockService
     * @param StoreStockService $storeStockService
     */
    public function __construct(
        IndexStockService $indexStockService,
        StoreStockService $storeStockService
    ) {
        $this->indexStockService = $indexStockService;
        $this->storeStockService = $storeStockService;
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

        $stocks = $this->indexStockService->run($request);

        if ($request->expectsJson()) {
            return response($stocks);
        }

        return view('stocks.index')
            ->with('orders', $stocks)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $products = Product::all();
        return view('stocks.create')
            ->with('products', $products);
    }

    /**
     * @param StoreStockRequest $storeStockRequest
     * @return RedirectResponse
     */
    public function store(StoreStockRequest $storeStockRequest)
    {
        $data = $storeStockRequest->validated();
        $this->storeStockService->run($data);
        return redirect()->route('stocks.index');
    }

    public function updateAll(Request $request)
    {
        $products = $request->products;
        $failed = false;
        foreach ($products as $product) {
            $amount = $product['amount'];
            $product = Product::query()->findOrFail($product['id']);
            $product->quantity_in_stock += $amount;
            if (!$product->save()) {
                $failed = true;
            }
        }
        return response()->json([
            'hasFail' => $failed
        ]);
    }
}
