<?php

namespace App\Services\Upload;

use App\Models\Upload;

class StoreUploadService
{
    /**
     * @var Upload
     */
    private $upload;

    /**
     * StoreUploadService constructor.
     * @param Upload $upload
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function run($data)
    {
        $data['file_name'] = $data['file']->store('files/comments');

        return $this->upload->create($data);
    }
}
