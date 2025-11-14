<?php

namespace Ingenius\Products\Extensions;

use Illuminate\Database\Eloquent\Builder;
use Ingenius\Products\Interfaces\ProductExtensionInterface;

abstract class BaseProductExtension implements ProductExtensionInterface
{
    /**
     * Default implementation returns the same array
     */
    public function extendProductArray($product, array $productArray): array
    {
        return $productArray;
    }

    /**
     * Default implementation returns the same query
     */
    public function extendProductQuery(Builder $query): Builder
    {
        return $query;
    }

    /**
     * Default priority (middle)
     */
    public function getPriority(): int
    {
        return 50;
    }

    /**
     * Default name is the class name
     */
    public function getName(): string
    {
        $className = get_class($this);
        $parts = explode('\\', $className);
        return end($parts);
    }
}
