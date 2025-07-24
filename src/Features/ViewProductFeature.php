<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ViewProductFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'view-product';
    }

    public function getName(): string
    {
        return 'View product';
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
