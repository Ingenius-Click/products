<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class CreateCategoryFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'create-category';
    }

    public function getName(): string
    {
        return 'Create category';
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
