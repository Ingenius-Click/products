<?php

namespace Ingenius\Products\Features;

use Ingenius\Core\Interfaces\FeatureInterface;

class DeleteCategoryFeature implements FeatureInterface
{
    public function getIdentifier(): string
    {
        return 'delete-category';
    }

    public function getName(): string
    {
        return __('Delete category');
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
