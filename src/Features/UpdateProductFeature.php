<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class UpdateProductFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'update-product';
    }

    public function getName(): string
    {
        return __('Update product');
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
