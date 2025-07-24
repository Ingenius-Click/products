<?php

namespace Ingenius\Products\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Core\Support\PermissionsManager;
use Ingenius\Core\Traits\RegistersConfigurations;
use Ingenius\Products\Constants\CategoriesPermissions;
use Ingenius\Products\Constants\ProductsPermissions;

class PermissionServiceProvider extends ServiceProvider
{
    use RegistersConfigurations;

    /**
     * The package name.
     *
     * @var string
     */
    protected string $packageName = 'Products';

    /**
     * Boot the application events.
     */
    public function boot(PermissionsManager $permissionsManager): void
    {
        $this->registerPermissions($permissionsManager);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // Register package-specific permission config
        $configPath = __DIR__ . '/../../config/permissions.php';

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'products.permissions');
            $this->registerConfig($configPath, 'products.permissions', 'products');
        }
    }

    /**
     * Register the package's permissions.
     */
    protected function registerPermissions(PermissionsManager $permissionsManager): void
    {
        // Register Products package permissions
        $permissionsManager->registerMany([
            ProductsPermissions::PRODUCTS_VIEW => 'View products',
            ProductsPermissions::PRODUCTS_CREATE => 'Create products',
            ProductsPermissions::PRODUCTS_EDIT => 'Edit products',
            ProductsPermissions::PRODUCTS_DELETE => 'Delete products',
        ], $this->packageName, 'tenant');

        $permissionsManager->registerMany([
            CategoriesPermissions::CATEGORIES_VIEW => 'View categories',
            CategoriesPermissions::CATEGORIES_CREATE => 'Create categories',
            CategoriesPermissions::CATEGORIES_EDIT => 'Edit categories',
            CategoriesPermissions::CATEGORIES_DELETE => 'Delete categories',
        ], $this->packageName, 'tenant');
    }
}
