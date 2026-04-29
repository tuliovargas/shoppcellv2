<?php

namespace App\Services\Product;

use App\Models\BrandModel;

class UpdateProductService
{
    /**
     * @param $data
     * @param $product
     * @return mixed
     */
    public function run($data, $product)
    {
        $data['user_id'] = auth()->user()->id;

        if (isset($data['photo'])) {
            $actualPhoto = storage_path() . '/' . $product->photo;

            if(file_exists($actualPhoto) && !is_dir($actualPhoto)){
                unlink($actualPhoto);
            }

            $path = $data['photo']->store('images/products');
            $data['photo'] = $path;
        }

        if (isset($data['observation']) && $data['observation'] === 'null') {
            $data['observation'] = null;
        }
        if (isset($data['barcode']) && $data['barcode'] === 'null') {
            $data['barcode'] = null;
        }

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
        if(isset($data['brand_id'])){
            if($data['brand_id'] > 1){
                $brandModel = BrandModel::where('brand_id', $data['brand_id'])
                    ->where('name', $data['brand_model'])
                    ->first();

                if($brandModel){
                    $data['brand_model_id'] = $brandModel->id;
                } else{
                    $brandModel = BrandModel::create([
                        'name' => $data['brand_model'],
                        'brand_id' => $data['brand_id'],
                    ]);

                    $data['brand_model_id'] = $brandModel->id;
                }
            }
        }

        $product->update($data);

        if(isset($data['category_id']) || isset($data['sub_category'])){
            $product->categories()->detach();
            if (isset($data['sub_category'])) {
                $product->categories()->attach($data['sub_category']);
            } elseif (isset($data['category_id'])){
                $product->categories()->attach($data['category_id']);
            }
        }

        return $product;
    }

    private function booleanfy($value)
    {
        if (!isset($value)) return false;
        return $value == 'true';
    }
}
