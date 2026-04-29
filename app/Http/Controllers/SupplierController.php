<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\SupplierRequest;
use App\Models\Supplier;
use App\Services\Supplier\{DeleteSupplierService, IndexSupplierService, StoreSupplierService, UpdateSupplierService,};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * @param Request $request
     * @param IndexSupplierService $indexSupplierService
     * @return Application|Factory|View
     */
    public function index(Request $request, IndexSupplierService $indexSupplierService)
    {

        if ($request->expectsJson()) {
            return $indexSupplierService->run($request);
        }
        $request->merge([
            'paginate' => 'false',
            'category' =>  0,
        ]);
        $suppliers = $indexSupplierService->run($request);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * @param SupplierRequest $supplierRequest
     * @param StoreSupplierService $storeSupplierService
     * @return RedirectResponse
     */
    public function store(SupplierRequest $supplierRequest, StoreSupplierService $storeSupplierService)
    {
        $data = $supplierRequest->validated();

        $supplier = $storeSupplierService->run($data);
        if ($supplierRequest->expectsJson()) {
            return $supplier;
        }

        return redirect()->route('suppliers.index');
    }

    /**
     * @param Supplier $supplier
     * @return Application|Factory|View
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * @param SupplierRequest $supplierRequest
     * @param UpdateSupplierService $updateSupplierService
     * @param Supplier $supplier
     * @return RedirectResponse
     */
    public function update(SupplierRequest $supplierRequest, UpdateSupplierService $updateSupplierService, Supplier $supplier)
    {
        $data = $supplierRequest->validated();
        unset($data['address']);

        $sup = $updateSupplierService->run($supplier, $data);
        if($supplierRequest->expectsJson()){
            return $sup;
        }

        return redirect()->route('suppliers.index');
    }

    /**
     * @param Supplier $supplier
     * @return Application|Factory|View
     */
    public function delete(Supplier $supplier)
    {
        return view('suppliers.delete', compact('supplier'));
    }

    /**
     * @param DeleteSupplierService $deleteSupplierService
     * @param Supplier $supplier
     * @return RedirectResponse
     */
    public function destroy(DeleteSupplierService $deleteSupplierService, Supplier $supplier)
    {
        $deleteSupplierService->run($supplier);

        return redirect()->route('suppliers.index')->with([
            'success' => true,
        ]);
    }

    public function show($id){
        $supplier = Supplier::query()->findOrFail($id);
        
        $supplier->load('address');
        return $supplier;
    }
}
