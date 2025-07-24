<?php

namespace Ingenius\Products\Actions;

use Ingenius\Products\Models\Category;

class ShowCategoryAction
{
    /**
     * Show a specific category
     *
     * @param Category $category
     * @return Category
     */
    public function __invoke(Category $category): Category
    {
        return $category;
    }
}
