<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class ListCategoriesFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'list-categories';
    }

    public function getName(): string
    {
        return 'List categories';
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
