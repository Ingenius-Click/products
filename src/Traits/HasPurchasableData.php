<?php

namespace Ingenius\Products\Traits;

use Ingenius\Core\Services\PackageHookManager;

trait HasPurchasableData
{
    public function getFinalPrice(): int
    {
        $basePrice = $this->sale_price;

        // Allow other packages to modify the final price
        // This enables discounts and other price modifications
        $hookManager = app(PackageHookManager::class);

        return $hookManager->execute('product.final_price', $basePrice, [
            'product_id' => $this->id,
            'product_class' => get_class($this),
            'base_price' => $basePrice,
            'regular_price' => $this->regular_price,
        ]);
    }

    public function getShowcasePrice(): int
    {
        $basePrice = $this->sale_price;

        // Allow other packages to modify the showcase price
        $hookManager = app(PackageHookManager::class);

        return $hookManager->execute('product.showcase_price', $basePrice, [
            'product_id' => $this->id,
            'product_class' => get_class($this),
            'base_price' => $basePrice,
            'regular_price' => $this->regular_price,
        ]);
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
