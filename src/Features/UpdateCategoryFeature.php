<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class UpdateCategoryFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'update-category';
    }

    public function getName(): string
    {
        return 'Update category';
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
