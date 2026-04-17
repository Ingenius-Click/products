<?php

namespace Ingenius\Products\Providers;

use Illuminate\Support\ServiceProvider;
use Ingenius\Core\Support\PermissionsManager;
use Ingenius\Core\Traits\RegistersConfigurations;
use Ingenius\Products\Constants\AttributesPermissions;
use Ingenius\Products\Constants\CategoriesPermissions;
use Ingenius\Products\Constants\ProductsPermissions;
use Ingenius\Products\Constants\VariantsPermissions;

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
        // Register Products permissions
        $permissionsManager->register(
            ProductsPermissions::PRODUCTS_VIEW,
            'View products',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.view_products'),
            __('products::permissions.groups.products')
        );

        $permissionsManager->register(
            ProductsPermissions::PRODUCTS_CREATE,
            'Create products',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.create_products'),
            __('products::permissions.groups.products')
        );

        $permissionsManager->register(
            ProductsPermissions::PRODUCTS_EDIT,
            'Edit products',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.edit_products'),
            __('products::permissions.groups.products')
        );

        $permissionsManager->register(
            ProductsPermissions::PRODUCTS_DELETE,
            'Delete products',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.delete_products'),
            __('products::permissions.groups.products')
        );

        // Register Categories permissions
        $permissionsManager->register(
            CategoriesPermissions::CATEGORIES_VIEW,
            'View categories',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.view_categories'),
            __('products::permissions.groups.categories')
        );

        $permissionsManager->register(
            CategoriesPermissions::CATEGORIES_CREATE,
            'Create categories',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.create_categories'),
            __('products::permissions.groups.categories')
        );

        $permissionsManager->register(
            CategoriesPermissions::CATEGORIES_EDIT,
            'Edit categories',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.edit_categories'),
            __('products::permissions.groups.categories')
        );

        $permissionsManager->register(
            CategoriesPermissions::CATEGORIES_DELETE,
            'Delete categories',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.delete_categories'),
            __('products::permissions.groups.categories')
        );

        // Register Attributes permissions
        $permissionsManager->register(
            AttributesPermissions::ATTRIBUTES_VIEW,
            'View attributes',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.view_attributes'),
            __('products::permissions.groups.attributes')
        );

        $permissionsManager->register(
            AttributesPermissions::ATTRIBUTES_CREATE,
            'Create attributes',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.create_attributes'),
            __('products::permissions.groups.attributes')
        );

        $permissionsManager->register(
            AttributesPermissions::ATTRIBUTES_EDIT,
            'Edit attributes',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.edit_attributes'),
            __('products::permissions.groups.attributes')
        );

        $permissionsManager->register(
            AttributesPermissions::ATTRIBUTES_DELETE,
            'Delete attributes',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.delete_attributes'),
            __('products::permissions.groups.attributes')
        );

        // Register Variants permissions
        $permissionsManager->register(
            VariantsPermissions::VARIANTS_VIEW,
            'View variants',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.view_variants'),
            __('products::permissions.groups.variants')
        );

        $permissionsManager->register(
            VariantsPermissions::VARIANTS_CREATE,
            'Create variants',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.create_variants'),
            __('products::permissions.groups.variants')
        );

        $permissionsManager->register(
            VariantsPermissions::VARIANTS_EDIT,
            'Edit variants',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.edit_variants'),
            __('products::permissions.groups.variants')
        );

        $permissionsManager->register(
            VariantsPermissions::VARIANTS_DELETE,
            'Delete variants',
            $this->packageName,
            'tenant',
            __('products::permissions.display_names.delete_variants'),
            __('products::permissions.groups.variants')
        );
    }
}
