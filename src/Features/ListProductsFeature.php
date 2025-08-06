<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ListProductsFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'list-products';
    }

    public function getName(): string
    {
        return __('List products');
    }

    public function getGroup(): string
    {
        return __('Products');
    }

    public function getPackage(): string
    {
        return 'products';
    }

    public function isBasic(): bool
    {
        return true;
    }
}
