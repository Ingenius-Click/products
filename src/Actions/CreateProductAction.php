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

            $data['regular_price'] = $data['normal_regular_price'] * 100;
            $data['sale_price'] = $data['normal_sale_price'] * 100;

            $product = Product::create($data);

            if (isset($data['new_images'])) {
                $this->saveImages($data['new_images'], $product);
            }

            if (isset($data['categories_ids'])) {
                $product->categories()->sync($data['categories_ids']);
            }

            return $product;
        });
    }
}
