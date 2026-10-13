<?php

namespace App\Services\Product;

use App\Models\Product;
use Yajra\DataTables\DataTables;

class DataTableProductService
{
    public function run()
    {
        $products = Product::query()
            ->select('products.id', 'products.name', 'products.brand_model_id')
            ->with('brandModel:id,photo_url');

        return DataTables::of($products)
            ->addColumn('image', function ($row) {
                $photo = $row->brandModel && !empty($row->brandModel->photo_url)
                    ? asset('storage/' . $row->brandModel->photo_url)
                    : asset('images/default-image.png');

                return '<img width="48" height="48" src="' . e($photo) . '">';
            })
            ->addColumn('action', function ($row) {
                $editButton = '<a href="' . route('products.edit', ['product' => $row->id]) . '"><i class="mx-2 fas fa-pen"></i></a>';
                $deleteButton = '<a href="' . route('products.delete', ['product' => $row->id]) . '"><i class="mx-2 fas fa-trash"></i></a>';

                return $editButton . $deleteButton;
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }
}
