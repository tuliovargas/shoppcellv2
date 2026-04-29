<?php

namespace App\Services\BrandModel;

use App\Models\BrandModel;

class IndexBrandModelService
{
    /**
     * @var BrandModel
     */
    private $brandModel;

    /**
     * IndexBrandModelService constructor.
     * @param BrandModel $brandModel
     */
    public function __construct(BrandModel $brandModel)
    {
        $this->brandModel = $brandModel;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = $request['search'] ?? '';
        $brand_id = $request['brand_id'] ?? '';
        $order_by = $request['order_by'] ?? '';
        $order = $request['order'] ?? '';
        $id = $request['id'] ?? '';

        $query = $this->brandModel->with(['brand'])
            ->where('status', 1)
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->when(!empty($brand_id), function ($query) use ($brand_id) {
                return $query->whereHas('brand', function ($query) use ($brand_id) {
                    $query->where('brands.id', $brand_id);
                });
            })
            ->when(!empty($id), function($query) use ($id){
                return $query->where('id', '=', $id);
            })
            ->when(!empty($order_by), function ($query) use ($request, $order_by, $order){
                return $query->orderBy($order_by, $order);
            });

        if ($request->all === 'true') {
            return $query->get();
        }

        if ($request->paginate === 'false') {
            return $query->limit(20)->get();
        }

        return $query->paginate(100);
    }
}
