<?php

namespace Ingenius\Products\Services;

use Illuminate\Database\Eloquent\Builder;
use Ingenius\Products\Interfaces\ProductExtensionInterface;

class ProductExtensionManager
{
    /**
     * Collection of registered extensions
     *
     * @var array<ProductExtensionInterface>
     */
    protected array $extensions = [];

    /**
     * Register a new product extension
     *
     * @param ProductExtensionInterface $extension
     * @return void
     */
    public function register(ProductExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
        // Sort extensions by priority when a new one is added
        $this->sortExtensions();
    }

    /**
     * Get all registered extensions, sorted by priority
     *
     * @return array<ProductExtensionInterface>
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * Extend the product array with data from all extensions
     *
     * @param mixed $product The product model instance
     * @param array $productArray The current product array
     * @return array The extended product array
     */
    public function extendProductArray($product, array $productArray): array
    {
        $result = $productArray;

        foreach ($this->extensions as $extension) {
            $result = $extension->extendProductArray($product, $result);
        }

        return $result;
    }

    /**
     * Extend the product query with modifications from all extensions
     *
     * @param Builder $query The product query builder
     * @return Builder The modified query builder
     */
    public function extendProductQuery(Builder $query): Builder
    {
        $result = $query;

        foreach ($this->extensions as $extension) {
            $result = $extension->extendProductQuery($result);
        }

        return $result;
    }

    /**
     * Sort extensions by priority
     *
     * @return void
     */
    protected function sortExtensions(): void
    {
        usort($this->extensions, function (ProductExtensionInterface $a, ProductExtensionInterface $b) {
            return $a->getPriority() <=> $b->getPriority();
        });
    }
}
