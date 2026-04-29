<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductLogResource;
use App\Http\Resources\ProductStockLogResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\Product\IndexProductService;
use App\Services\Product\StoreProductService;
use App\Services\Product\UpdateProductService;
use App\Traits\FormatTrait;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use FormatTrait;

    /**
     * @var IndexProductService
     */
    private $indexProductService;

    /**
     * @var StoreProductService
     */
    private $storeProductService;

    /**
     * @var UpdateProductService
     */
    private $updateProductService;

    /**
     * ProductController constructor.
     * @param IndexProductService $indexProductService
     * @param StoreProductService $storeProductService
     * @param UpdateProductService $updateProductService
     */
    public function __construct(
        IndexProductService $indexProductService,
        StoreProductService $storeProductService,
        UpdateProductService $updateProductService
    ) {
        // $this->middleware('permission: register product')->except('index', 'edit');
        $this->indexProductService = $indexProductService;
        $this->storeProductService = $storeProductService;
        $this->updateProductService = $updateProductService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $search = null;
        if ($request->search) {
            $search = $request->search;
        }

        if ($request->expectsJson()) {
            $products = $this->indexProductService->run($request);
            return response($products);
        }

        $request->merge([
            'paginate' => 'false',
            'category' =>  0,
        ]);

        $products = $this->indexProductService->run($request);

        return view('products.index')
            ->with('products', $products)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * @param StoreProductRequest $storeProductRequest
     * @return RedirectResponse
     */
    public function store(StoreProductRequest $storeProductRequest)
    {
        $data = $storeProductRequest->validated();
        $product = $this->storeProductService->run($data);
        if ($storeProductRequest->expectsJson()) {
            return response($product);
        }
        return redirect()->route('products.index');
    }

    /**
     * @param Product $product
     * @return Application|Factory|View
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * @param Product $product
     * @return Application|Factory|View
     */
    public function show(Product $product)
    {
        $product->brandModel;

        $product->category = $product->categories->isNotEmpty() ?
            $product->categories->first()->id :
            '';

        $product->sub_category = $product->categories->count() > 1 ?
            $product->categories->getNth(1)->name :
            '';

        return $product;
    }

    /**
     * @param UpdateProductRequest $updateProductRequest
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(UpdateProductRequest $updateProductRequest, Product $product)
    {
        $data = $updateProductRequest->validated();
        $this->updateProductService->run($data, $product);
        return $product;
    }

    /**
     * @param Product $product
     * @return Application|Factory|View
     */
    public function delete(Product $product)
    {
        return view('products.delete', compact('product'));
    }

	public function log(Product $product)
	{
		return response( ["histories" => ProductLogResource::collection($product->logs), "stock_histories" => ProductStockLogResource::collection($product->stockLogs)] );
	}

    /**
     * @param Product $product
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Product $product)
    {
        $product->categories()->detach();
        $product->delete();
        return redirect()->route('products.index');
    }
}
