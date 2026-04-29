<?php

namespace App\Services\BrandModel;

class DeleteBrandModelService
{
    /**
     * @param $brandModel
     * @return mixed
     */
    public function run($brandModel)
    {
        return $brandModel->delete();
    }
}
