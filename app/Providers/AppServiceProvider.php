<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        Relation::morphMap([
            'client' => 'App\Models\Client',
            'supplier' => 'App\Models\Supplier',
            'comment' => 'App\Models\Comment',
        ]);

			Product::observe(ProductObserver::class);
    }
}
