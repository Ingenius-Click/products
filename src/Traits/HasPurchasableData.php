<?php

namespace Ingenius\Products\Traits;

use Ingenius\Core\Services\PackageHookManager;
use Ingenius\Products\Services\ProductPriceCacheService;

trait HasPurchasableData
{
    public function getFinalPrice(): int
    {
        // Check cache first
        $cacheService = app(ProductPriceCacheService::class);
        $cached = $cacheService->getFinalPrice($this->id, get_class($this));

        if ($cached !== null) {
            return $cached;
        }

        $basePrice = $this->sale_price;

        // Allow other packages to modify the final price
        // This enables discounts and other price modifications
        $hookManager = app(PackageHookManager::class);

        $finalPrice = $hookManager->execute('product.final_price', $basePrice, [
            'product_id' => $this->id,
            'product_class' => get_class($this),
            'base_price' => $basePrice,
            'regular_price' => $this->regular_price,
        ]);

        // Cache the result for the request lifecycle
        $cacheService->setFinalPrice($this->id, get_class($this), $finalPrice);

        return $finalPrice;
    }

    public function getShowcasePrice(): int
    {
        // Check cache first
        $cacheService = app(ProductPriceCacheService::class);
        $cached = $cacheService->getShowcasePrice($this->id, get_class($this));

        if ($cached !== null) {
            return $cached;
        }

        $basePrice = $this->sale_price;

        // Allow other packages to modify the showcase price
        $hookManager = app(PackageHookManager::class);

        $showcasePrice = $hookManager->execute('product.showcase_price', $basePrice, [
            'product_id' => $this->id,
            'product_class' => get_class($this),
            'base_price' => $basePrice,
            'regular_price' => $this->regular_price,
        ]);

        // Cache the result for the request lifecycle
        $cacheService->setShowcasePrice($this->id, get_class($this), $showcasePrice);

        return $showcasePrice;
    }

    public function getRegularPrice(): int
    {
        return $this->regular_price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

