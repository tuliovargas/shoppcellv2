<?php

namespace App\Services\SystemConfiguration;

use Illuminate\Support\Facades\Cache;
use App\Models\Supplier;
use App\Traits\UploadTrait;
use App\Models\Configuration;

class UpdateSystemConfigurationService
{

    use UploadTrait;

    public function run(Array $data)
    {

        foreach($data as $key => $value) {
            Configuration::where('key', $key)
            ->update(['value' => $value]);
        }

        if(isset($data['photo'])) {
            $logo_url = $this->uploadFile($data['photo'], 'config', 'logo');
            Configuration::where('key', 'logo')
                ->update(['value' => 'storage/' . $logo_url]);
        }
    }
}
