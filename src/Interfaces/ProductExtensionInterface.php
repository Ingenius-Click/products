<?php

namespace Ingenius\Products\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface ProductExtensionInterface
{
    /**
     * Extend the product array/resource with additional data
     *
     * This method is called when a product is being transformed to an array
     * (e.g., in API resources). Use this to add additional data like reviews,
     * ratings, related products, etc.
     *
     * @param mixed $product The product model instance
     * @param array $productArray The current product array
     * @return array The modified product array with additional data
     */
    public function extendProductArray($product, array $productArray): array;

    /**
     * Extend the product query with additional data loading
     *
     * This method is called before products are fetched from the database.
     * Use this to eager load relationships or modify the query to optimize
     * data fetching for your extension.
     *
     * @param Builder $query The product query builder
     * @return Builder The modified query builder
     */
    public function extendProductQuery(Builder $query): Builder;

    /**
     * Get the priority of this extension
     * Lower numbers run first
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Get the name of this extension
     *
     * @return string
     */
    public function getName(): string;
}
