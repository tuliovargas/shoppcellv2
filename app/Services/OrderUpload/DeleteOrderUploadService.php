<?php

namespace App\Services\OrderUpload;

use App\Models\Upload;

class DeleteOrderUploadService
{
    /**
     * @param $uploadableId
     * @param $uploadableType
     * @return mixed
     */
    public function run($upload)
    {
        return $upload->delete();
    }
}
