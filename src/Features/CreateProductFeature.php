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
        return 'Create product';
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
