<?php

namespace Ingenius\Products\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Core\Services\FeatureManager;
use Ingenius\Core\Traits\RegistersConfigurations;
use Ingenius\Core\Traits\RegistersMigrations;
use Ingenius\Products\Features\CreateCategoryFeature;
use Ingenius\Products\Features\CreateProductFeature;
use Ingenius\Products\Features\DeleteCategoryFeature;
use Ingenius\Products\Features\DeleteProductFeature;
use Ingenius\Products\Features\ListCategoriesFeature;
use Ingenius\Products\Features\ListProductsFeature;
use Ingenius\Products\Features\UpdateCategoryFeature;
use Ingenius\Products\Features\UpdateProductFeature;
use Ingenius\Products\Features\ViewCategoryFeature;
use Ingenius\Products\Features\ViewProductFeature;

class ProductsServiceProvider extends ServiceProvider
{
    use RegistersMigrations, RegistersConfigurations;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/products.php', 'products');

        // Register configuration with the registry
        $this->registerConfig(__DIR__ . '/../../config/products.php', 'products', 'products');

        // Register the route service provider
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(PermissionServiceProvider::class);
        $this->app->register(SlugServiceProvider::class);

        $this->app->afterResolving(FeatureManager::class, function (FeatureManager $manager) {
            $manager->register(new ListProductsFeature());
            $manager->register(new CreateProductFeature());
            $manager->register(new ViewProductFeature());
            $manager->register(new UpdateProductFeature());
            $manager->register(new DeleteProductFeature());
            $manager->register(new ListCategoriesFeature());
            $manager->register(new CreateCategoryFeature());
            $manager->register(new ViewCategoryFeature());
            $manager->register(new UpdateCategoryFeature());
            $manager->register(new DeleteCategoryFeature());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register migrations with the registry
        $this->registerMigrations(__DIR__ . '/../../database/migrations', 'products');

        // Check if there's a tenant migrations directory and register it
        $tenantMigrationsPath = __DIR__ . '/../../database/migrations/tenant';
        if (is_dir($tenantMigrationsPath)) {
            $this->registerTenantMigrations($tenantMigrationsPath, 'products');
        }

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Load views only if they exist
        $viewsPath = __DIR__ . '/../../resources/views';
        if (is_dir($viewsPath) && count(glob($viewsPath . '/*.blade.php')) > 0) {
            $this->loadViewsFrom($viewsPath, 'products');
            
            // Publish views only if they exist
            $this->publishes([
                $viewsPath => resource_path('views/vendor/products'),
            ], 'products-views');
        }

        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/products.php' => config_path('products.php'),
        ], 'products-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'products-migrations');
    }
}
