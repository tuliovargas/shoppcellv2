<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait UploadTrait
{
    public function uploadFile(UploadedFile $uploadedFile, String $folder, String $fileName = null)
    {
        if (isset($uploadedFile) && !empty($uploadedFile)) {
            $fileName = date('dmY') . time();

            $fileName = $fileName . '.' . $uploadedFile->getClientOriginalExtension();

            $file = $uploadedFile->storeAs($folder, $fileName, 'public');
            return $file;
        }

        return null;
    }
}
