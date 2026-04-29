<?php

namespace App\Services\SystemConfiguration;

use App\Models\Configuration;

class IndexSystemConfigurationService
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * IndexBrandModelService constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function run($request)
    {
        $keys = isset($request['keys']) ? $request['keys'] : '';

        $query = $this->configuration->when($keys, function ($query, $keys) {
            return $query->whereIn('key', explode(',', $keys));
        });

        return $query->get();
    }
}
