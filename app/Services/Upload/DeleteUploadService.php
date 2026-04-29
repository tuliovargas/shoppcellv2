<?php

namespace App\Services\Upload;

use App\Models\Upload;

class DeleteUploadService
{
    /**
     * @var Upload
     */
    private $upload;

    /**
     * DeleteUploadService constructor.
     * @param Upload $upload
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * @param $uploadableId
     * @param $uploadableType
     * @return mixed
     */
    public function run($uploadableId, $uploadableType)
    {
        return $this->upload->where('uploadable_id', $uploadableId)
            ->where('uploadable_type', $uploadableType)
            ->delete();
    }
}
