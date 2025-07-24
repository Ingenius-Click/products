<?php

namespace Ingenius\Products\Actions;

use Ingenius\Products\Models\Category;
use Illuminate\Support\Facades\DB;
use Ingenius\Core\Traits\HandleImages;

class UpdateCategoryAction
{
    use HandleImages;

    /**
     * Update an existing category
     *
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function __invoke(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            // Update the category
            $category->update($data);

            if (isset($data['images'])) {
                $this->saveImages($data['images'], $category, 'category_images');
            }

            return $category;
        });
    }
}
