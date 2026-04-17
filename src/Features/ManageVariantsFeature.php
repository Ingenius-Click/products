<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ManageVariantsFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'manage-variants';
    }

    public function getName(): string
    {
        return __('Manage Product Variants');
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
