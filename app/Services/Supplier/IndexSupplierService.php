<?php

namespace App\Services\Supplier;

use App\Models\Supplier;

class IndexSupplierService
{
    /**
     * @var Supplier
     */
    private $supplier;

    /**
     * IndexSupplierService constructor.
     * @param Supplier $supplier
     */
    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';

        $query = $this->supplier->with(['address'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            });

        if ($request->paginate === 'false') {
            return $query->get();
        }

        return $query->paginate(10);
    }
}
