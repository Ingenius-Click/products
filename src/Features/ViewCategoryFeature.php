<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ViewCategoryFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'view-category';
    }

    public function getName(): string
    {
        return __('View category');
    }

    public function getGroup(): string
    {
        return __('Categories');
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
