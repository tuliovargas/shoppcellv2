<?php

namespace App\Services\Stock;

use App\Models\Stock;

class IndexStockService
{
    /**
     * @var Stock
     */
    private $stock;

    /**
     * IndexStockService constructor.
     * @param Stock $stock
     */
    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $search = isset($request['search']) ? $request['search'] : '';

        $query = $this->stock->with('products')->when($search, function ($query, $search) {
            return $query->where('id', '=', $search);
        });

        if ($request->paginate === 'false') {
            return $this->stock->all();
        }

        return $query->paginate(10);
    }
}
