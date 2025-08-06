<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class CreateProductFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'create-product';
    }

    public function getName(): string
    {
        return __('Create product');
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
