<?php

namespace Ingenius\Products\Actions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Ingenius\Core\Traits\HandleImages;
use Ingenius\Products\Models\Product;

class UpdateProductAction
{
    use HandleImages;

    /**
     * Update a product
     *
     * @param Product $product
     * @param array $data
     * @return Product
     * @throws ModelNotFoundException
     */
    public function __invoke(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $data['regular_price'] = $data['normal_regular_price'] * 100;
            $data['sale_price'] = $data['normal_sale_price'] * 100;

            if (!isset($data['handle_stock']) || !$data['handle_stock']) {
                unset($data['stock']);
                unset($data['stock_for_sale']);
            }

            $product->update($data);

            if (isset($data['new_images'])) {
                $this->saveImages($data['new_images'], $product);
            }

            if (isset($data['removed_images'])) {
                $this->removeImages($data['removed_images'], $product);
            }

            if (isset($data['categories_ids'])) {
                $product->categories()->sync($data['categories_ids']);
            }

            return $product->fresh();
        });
    }
}
