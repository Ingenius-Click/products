<?php

namespace Ingenius\Products\Actions;

use Ingenius\Products\Models\Category;

class ListCategoriesAction
{
    /**
     * List all categories with optional filters
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function __invoke(array $filters = [])
    {
        $query = Category::query();

        return table_handler_paginate($filters, $query);
    }
}
