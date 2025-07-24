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

        // Apply visibility filter if specified
        if (isset($filters['visible'])) {
            $query->where('visible', filter_var($filters['visible'], FILTER_VALIDATE_BOOLEAN));
        }

        // Apply stock filter if specified
        if (isset($filters['in_stock'])) {
            $inStock = filter_var($filters['in_stock'], FILTER_VALIDATE_BOOLEAN);
            if ($inStock) {
                $query->where('stock_for_sale', '>', 0);
            }
        }

        // Get per page value or default to 15
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }
}
