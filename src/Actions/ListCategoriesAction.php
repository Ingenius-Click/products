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

        // Apply parent filter if specified
        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        // Apply search filter if specified
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Get per page value or default to 15
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }
}
