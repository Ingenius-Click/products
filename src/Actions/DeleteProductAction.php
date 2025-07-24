<?php

namespace Ingenius\Products\Actions;

use Illuminate\Support\Facades\DB;
use Ingenius\Products\Models\Product;

class DeleteProductAction
{
    /**
     * Delete a product
     *
     * @param Product $product
     * @return bool
     */
    public function __invoke(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            return $product->delete();
        });
    }
}
