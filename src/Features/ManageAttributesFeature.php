<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ManageAttributesFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'manage-attributes';
    }

    public function getName(): string
    {
        return __('Manage Attributes');
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
