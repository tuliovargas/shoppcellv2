<?php

namespace App\Services\BrandModel;

use App\Traits\UploadTrait;

class UpdateBrandModelService
{
    use UploadTrait;
    /**
     * @param $data
     * @param $brandModel
     * @return mixed
     */
    public function run($data, $brandModel)
    {
        if (isset($data['photo_url'])) {
            if (!empty($brandModel->photo_url)) {
                $file = storage_path() . '/app/public/' . $brandModel->photo_url;
                if (file_exists($file) && !is_dir($file)) {
                    unlink($file);
                }
            }           
            $data['photo_url'] = $this->uploadFile($data['photo_url'], '/images/phones');
        }
        $brandModel->update($data);

        return $brandModel;
    }
}
