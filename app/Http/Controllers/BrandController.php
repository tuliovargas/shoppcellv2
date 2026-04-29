<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use App\Services\Brand\IndexBrandService;
use Exception;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private $indexBrandService;

    public function __construct(Brand $brand, IndexBrandService $indexBrandService)
    {
        $this->indexBrandService = $indexBrandService;
        $this->brand = $brand;
    }

    public function index(Request $request)
    {
        $search = null;
        if ($request->search) {
            $search = $request->search;
        }

        $brands = $this->indexBrandService->run($request);

        if ($request->expectsJson()) {
            return response($brands);
        }

        return view('brands.index')
            ->with('brands', $brands)
            ->with('search', $search);
    }


    public function create()
    {
        return view('brands.create');
    }


    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('photo_url') && $request->photo_url->isValid()) {
            $imagePath = $request->photo_url->store('/brands');

            $data['photo_url'] = $imagePath;
        }

        $this->brand->create($data);
        return redirect()->route('brands.index')->with([
            'success' => true,
            'message' => 'Marca cadastrada com sucesso'
        ]);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {

        $brand = $this->brand->find($id);
        return view('brands.edit', [
            'brand' => $brand,
        ]);
    }


    public function update(Request $request, $id)
    {
        $brand = $this->brand->find($id);
        $data = $request->all();
        if ($request->hasFile('photo_url') && $request->photo_url->isValid()) {
            $imagePath = $request->photo_url->store('/brands');

            $data['photo_url'] = $imagePath;
        }

        $brand->update($data);

        return redirect()->route('brands.index')->with([
            'success' => true,
            'message' => 'Marca editada com sucesso'
        ]);
    }


    public function delete($id)
    {
        $brand = $this->brand->find($id);

        if ($brand->photo_url && Storage::exists($brand->photo_url)) {
            Storage::delete($brand->photo_url);
        }
        try {
            $brand->delete();
        } catch (Exception $ex) {
            return redirect()->route('brands.index')->with([
                'success' => false,
                'message' => 'Não foi possível remover a marca'
            ]);
        }
        return redirect()->route('brands.index')->with([
            'success' => true,
            'message' => 'Marca removida com sucesso'
        ]);
    }
}
