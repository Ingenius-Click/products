<?php

namespace Ingenius\Products\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Core\Traits\RegistersConfigurations;

class SlugServiceProvider extends ServiceProvider
{
    use RegistersConfigurations;

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sluggable.php',
            'products.sluggable'
        );

        // Register configuration with the registry
        $this->registerConfig(
            __DIR__ . '/../../config/sluggable.php',
            'products.sluggable',
            'products'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/sluggable.php' => config_path('products/sluggable.php'),
        ], 'products-config');
    }
}
