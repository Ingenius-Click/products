<?php

namespace Ingenius\Products\Actions;

use Ingenius\Products\Models\Category;
use Illuminate\Support\Facades\DB;
use Ingenius\Core\Traits\HandleImages;

class CreateCategoryAction
{
    use HandleImages;

    /**
     * Create a new category
     *
     * @param array $data
     * @return Category
     */
    public function __invoke(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            // Create the new category
            $category = Category::create($data);

            if (isset($data['images'])) {
                $this->saveImages($data['images'], $category, 'category_images');
            }

            return $category;
        });
    }
}
