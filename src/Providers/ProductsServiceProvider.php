<?php

namespace Ingenius\Products\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Ingenius\Core\Services\FeatureManager;
use Ingenius\Core\Services\PackageHookManager;
use Ingenius\Core\Services\StoreConfigurationManager;
use Ingenius\Core\Traits\RegistersConfigurations;
use Ingenius\Core\Traits\RegistersMigrations;
use Ingenius\Products\Configuration\ProductStoreConfiguration;
use Ingenius\Products\Features\ComingSoonProductFeature;
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
use Ingenius\Products\Services\ProductExtensionManager;

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

        // Register settings classes
        $this->registerSettingsClasses();

        // Register settings bindings
        $this->registerSettingsBindings();

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
            $manager->register(new ComingSoonProductFeature());
        });

        // Register the product extension manager as a singleton
        $this->app->singleton(ProductExtensionManager::class, fn() => new ProductExtensionManager());

        // Register the product price cache service as a singleton (request-scoped)
        $this->app->singleton(\Ingenius\Products\Services\ProductPriceCacheService::class);

        // Register store configuration extension
        $this->app->afterResolving(StoreConfigurationManager::class, function (StoreConfigurationManager $manager) {
            $manager->register(new ProductStoreConfiguration());
        });

        $this->app->afterResolving(PackageHookManager::class, function (PackageHookManager $hookManager) {
            $hookManager->register('products.query.coming_soon', function ($query, $params) {
                if (method_exists($query->getModel(), 'scopeComingSoon')) {
                    $query->comingSoon();
                } else {
                    $query->where('coming_soon', true);
                }

                return $query;
            });

            // Hook to warm price cache when cart items are loaded
            $hookManager->register('cart.items.loaded', function ($products, $context) {
                if (!empty($products)) {
                    $priceCache = app(\Ingenius\Products\Services\ProductPriceCacheService::class);
                    $priceCache->warmBulkPrices($products);
                }

                return $products;
            }, priority: 10); // High priority to run early
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

    /**
     * Register settings classes with the core settings system.
     */
    protected function registerSettingsClasses(): void
    {
        // Get existing settings classes from core config
        $coreSettingsClasses = Config::get('settings.settings_classes', []);

        // Get products settings classes
        $productsSettingsClasses = Config::get('products.settings_classes', []);

        // Merge and update the core settings classes
        $mergedSettingsClasses = array_merge($coreSettingsClasses, $productsSettingsClasses);

        // Update the core settings config
        Config::set('settings.settings_classes', $mergedSettingsClasses);
    }

    /**
     * Register Settings class bindings that work in tenant context
     */
    protected function registerSettingsBindings(): void
    {
        // Bind ProductSettings to use make() method when in tenant context
        $this->app->bind(\Ingenius\Products\Settings\ProductSettings::class, function ($app) {
            // Check if we're in tenant context
            $tenancy = $app->make(\Stancl\Tenancy\Tenancy::class);
            if ($tenancy->tenant) {
                return \Ingenius\Products\Settings\ProductSettings::make();
            }

            // Return empty instance if not in tenant context
            return new \Ingenius\Products\Settings\ProductSettings();
        });
    }
}
