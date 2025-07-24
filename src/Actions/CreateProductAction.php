<?php

namespace Ingenius\Products\Actions;

use Ingenius\Products\Models\Product;
use Illuminate\Support\Facades\DB;
use Ingenius\Core\Traits\HandleImages;

class CreateProductAction
{
    use HandleImages;

    /**
     * Create a new product
     *
     * @param array $data
     * @return Product
     */
    public function __invoke(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Create the new product

            $product = Product::create($data);

            if (isset($data['images'])) {
                $this->saveImages($data['images'], $product);
            }

            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            return $product;
        });
    }
}
