<?php

namespace Ingenius\Products\Actions;

use Ingenius\Products\Models\Product;

class ShowProductAction
{
    /**
     * Show a specific product
     *
     * @param Product $product
     * @return Product
     */
    public function __invoke(Product $product): Product
    {
        return $product;
    }
}
