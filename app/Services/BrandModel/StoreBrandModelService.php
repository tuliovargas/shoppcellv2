<?php

namespace App\Services\BrandModel;

use App\Models\BrandModel;
use App\Traits\UploadTrait;

class StoreBrandModelService
{
    use UploadTrait;
    /**
     * @var BrandModel
     */
    private $brandModel;

    /**
     * StoreBrandModelService constructor.
     * @param BrandModel $brandModel
     */
    public function __construct(BrandModel $brandModel)
    {
        $this->brandModel = $brandModel;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        if (isset($data['photo_url'])) {
            $data['photo_url'] = $this->uploadFile($data['photo_url'], '/images/phones');
        }
        return $this->brandModel->create($data);
    }
}
