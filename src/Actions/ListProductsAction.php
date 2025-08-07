<?php

namespace Ingenius\Products\Actions;

use Ingenius\Products\Models\Product;

class ListProductsAction
{
    /**
     * List all products with optional filters
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function __invoke(array $filters = [])
    {
        $query = Product::query();

        return table_handler_paginate($filters, $query);
    }
}
