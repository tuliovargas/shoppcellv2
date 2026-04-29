<?php

namespace App\Services\BrandModel;

use App\Models\BrandModel;
use stdClass;

class StoreNewBrandModelService
{
    /**
     * @var Product
     */
    private $brand_model;

    /**
     * StoreProductService constructor.
     * @param Product $product
     */
    public function __construct(BrandModel $brand_model)
    {
        $this->brand_model = $brand_model;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        $brand_model = $this->brand_model->where('name', $data['brand_model'])->first();

        if (!$brand_model) {
            $brand_model = $this->brand_model->create([
                'name' => $data['brand_model'],
                'brand_id' => $data['brand_id']
            ]);
        }

        if (isset($data['photo'])) {
            $path = $data['photo']->store('images/phones');
            $updateds['photo'] = $path;
            $brand_model->photo_url = $path;
            $brand_model->save();
        }

        return $brand_model;
    }
}
