<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandModel\StoreBrandModelRequest;
use App\Http\Requests\BrandModel\UpdateBrandModelRequest;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Services\BrandModel\DataTableBrandModelService;
use App\Services\BrandModel\DeleteBrandModelService;
use App\Services\BrandModel\IndexBrandModelService;
use App\Services\BrandModel\StoreBrandModelService;
use App\Services\BrandModel\UpdateBrandModelService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandModelController extends Controller
{
    /**
     * @var DeleteBrandModelService
     */
    private $deleteBrandModelService;

    /**
     * @var UpdateBrandModelService
     */
    private $updateBrandModelService;

    /**
     * @var StoreBrandModelService
     */
    private $storeBrandModelService;

    /**
     * @var IndexBrandModelService
     */
    private $indexBrandModelService;

    /**
     * BrandModelController constructor.
     * @param IndexBrandModelService $indexBrandModelService
     * @param StoreBrandModelService $storeBrandModelService
     * @param UpdateBrandModelService $updateBrandModelService
     * @param DeleteBrandModelService $deleteBrandModelService
     */
    public function __construct(
        IndexBrandModelService $indexBrandModelService,
        StoreBrandModelService $storeBrandModelService,
        UpdateBrandModelService $updateBrandModelService,
        DeleteBrandModelService $deleteBrandModelService
    ) {
        $this->indexBrandModelService = $indexBrandModelService;
        $this->storeBrandModelService = $storeBrandModelService;
        $this->updateBrandModelService = $updateBrandModelService;
        $this->deleteBrandModelService = $deleteBrandModelService;
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Factory|View|Response
     */
    public function index(Request $request, DataTableBrandModelService $dataTableBrandModelService)
    {
        if ($request->ajax() && $request->get('type') === 'datatables') {
            return $dataTableBrandModelService->run();
        }

        $search = null;
        if ($request->search) {
            $search = $request->search;
        }

        $brandModels = $this->indexBrandModelService->run($request);

        if ($request->expectsJson()) {
            return response($brandModels);
        }

        return view('brand-models.index')
            ->with('brandModels', $brandModels)
            ->with('search', $search);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $brands = Brand::all();
        return view('brand-models.create')
            ->with('brands', $brands);
    }

    /**
     * @param StoreBrandModelRequest $storeBrandModelRequest
     * @return RedirectResponse
     */
    public function store(Request $request, StoreBrandModelRequest $storeBrandModelRequest)
    {
        $data = $storeBrandModelRequest->validated();
        $brandModel = $this->storeBrandModelService->run($data);
				if ($request->type === 'vue') {
					return response($brandModel);
				} else {
					return redirect()->route('brand-models.index')->with([
                        'success' => true,
                        'message' => 'Modelo cadastrado com sucesso'
                    ]);
				}
    }

    /**
     * @param BrandModel $brandModel
     * @return Application|Factory|View
     */
    public function edit(BrandModel $brandModel)
    {
        $brands = Brand::all();
        return view('brand-models.edit')
            ->with('brands', $brands)
            ->with('brandModel', $brandModel);
    }

    /**
     * @param UpdateBrandModelRequest $updateBrandModelRequest
     * @param BrandModel $brandModel
     * @return RedirectResponse
     */
    public function update(UpdateBrandModelRequest $updateBrandModelRequest, BrandModel $brandModel)
    {
        $data = $updateBrandModelRequest->validated();
        $this->updateBrandModelService->run($data, $brandModel);
        return redirect()->route('brand-models.index')->with([
            'success' => true,
            'message' => 'Modelo editado com sucesso'
        ]);
    }

    /**
     * @param BrandModel $brandModel
     * @return RedirectResponse
     */
    public function delete(BrandModel $brandModel)
    {
        return view('brand-models.delete')
            ->with('brandModel', $brandModel);
    }

    /**
     * @param BrandModel $brandModel
     * @return RedirectResponse
     */
    public function destroy(BrandModel $brandModel)
    {
        $this->deleteBrandModelService->run($brandModel);
        return redirect()->route('brand-models.index')->with([
            'success' => true,
            'message' => 'Modelo removido com sucesso'
        ]);
    }
}
