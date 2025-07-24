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
            $product->update($data);

            if (isset($data['images'])) {
                $this->saveImages($data['images'], $product);
            }

            if (isset($data['removed_images'])) {
                $this->removeImages($data['removed_images'], $product);
            }

            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            return $product->fresh();
        });
    }
}
