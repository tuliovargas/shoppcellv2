<?php

namespace App\Services\Brand;

use App\Models\Brand;

class IndexBrandService
{

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function run($request)
    {
        $search = $request['search'] ?? '';
        $order_by = $request['order_by'] ?? '';
        $order = $request['order'] ?? '';
        $id = $request['id'] ?? '';

        $query = $this->brand
            ->when(!empty($search), function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->when(!empty($id), function($query) use($id){
                return $query->where('id', '=', $id);
            })
            ->when(!empty($order_by), function ($query) use ($request, $order_by, $order){
                return $query->orderBy($order_by, $order);
            });

        return $query->get();
    }
}
