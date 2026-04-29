<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuration;
use Illuminate\Support\Facades\Schema;

class ConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if(Schema::hasTable('configurations')) {

            config([
                'global' => Configuration::all([
                    'key','value'
                    ])
                    ->keyBy('key')
                    ->transform(function ($config) {
                        return $config->value;
                    })
                    ->toArray()
            ]);
        }
    }
}
