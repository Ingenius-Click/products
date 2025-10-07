<?php

namespace Ingenius\Products\Configuration;

use Illuminate\Support\Facades\Auth;
use Ingenius\Core\Interfaces\StoreConfigurationInterface;

class ProductStoreConfiguration implements StoreConfigurationInterface {

    /**
     * Get the configuration key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return 'products';
    }

    /**
     * Get the configuration value.
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        $productSettings = app(\Ingenius\Products\Settings\ProductSettings::class);

        return [
            'auto_sku_generation' => $productSettings->auto_sku_generation
        ];
    }

    /**
     * Get the package name that provides this configuration.
     *
     * @return string
     */
    public function getPackageName(): string
    {
        return 'products';
    }

    /**
     * Get the priority for this configuration (higher number = higher priority).
     *
     * @return int
     */
    public function getPriority(): int
    {
        // High priority since this overrides the base_coin from core settings
        return 50;
    }

    /**
     * Check if this configuration is available/enabled.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        // Check if we're in a tenant context
        if (!tenant()) {
            return false;
        }

        // Check if user is authenticated via Sanctum or tenant guard
        return request()->user('sanctum') || Auth::guard('tenant')->check();
    }

}