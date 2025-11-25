<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ComingSoonProductFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'coming-soon-product';
    }

    public function getName(): string
    {
        return __('Coming Soon Products');
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
        return false;
    }
}
