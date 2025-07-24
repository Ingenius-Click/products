<?php

namespace Ingenius\Products\Actions;

use Ingenius\Products\Models\Category;
use Illuminate\Support\Facades\DB;

class DeleteCategoryAction
{
    /**
     * Delete a category
     *
     * @param Category $category
     * @return void
     */
    public function __invoke(Category $category): void
    {
        DB::transaction(function () use ($category) {
            $category->delete();
        });
    }
}
