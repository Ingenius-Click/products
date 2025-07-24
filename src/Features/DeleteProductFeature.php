<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class DeleteProductFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'delete-product';
    }

    public function getName(): string
    {
        return 'Delete product';
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
