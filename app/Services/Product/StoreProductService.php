<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Services\BrandModel\StoreNewBrandModelService;

class StoreProductService
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var StoreNewBrandModelService
     */
    private $storeNewBrandModelService;

    /**
     * StoreProductService constructor.
     * @param Product $product
     * @param StoreNewBrandModelService $storeNewBrandModelService
     */
    public function __construct(Product $product, StoreNewBrandModelService $storeNewBrandModelService)
    {
        $this->product = $product;
        $this->storeNewBrandModelService = $storeNewBrandModelService;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        $data['user_id'] = auth()->user()->id;

        if (isset($data['can_discount'])) {
            $data['can_discount'] = $this->booleanfy($data['can_discount']);
        }
        if (isset($data['can_commission'])) {
            $data['can_commission'] = $this->booleanfy($data['can_commission']);
        }
        if (isset($data['is_new'])) {
            $data['is_new'] = $this->booleanfy($data['is_new']);
        }
        if (isset($data['is_active'])) {
            $data['is_active'] = $this->booleanfy($data['is_active']);
        }

        $brand_model = $this->storeNewBrandModelService->run($data);
        $data['brand_model_id'] = $brand_model->id;

        $product = $this->product->create($data);

        if (isset($data['sub_category'])) {
            $product->categories()->attach($data['sub_category']);
        } else {
            $product->categories()->attach($data['category_id']);
        }

        return $product;
    }

    private function booleanfy($value)
    {
        if (!isset($value)) return false;
        return $value == 'true';
    }
}
