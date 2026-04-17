<?php

namespace Ingenius\Products\Actions\Variants;

use Ingenius\Products\Models\Product;
use Ingenius\Products\Models\ProductVariant;

class ListProductVariantsAction
{
    public function __invoke(Product $product, array $filters = [])
    {
        $query = ProductVariant::where('product_id', $product->id)
            ->with('attributeOptions.attribute')
            ->orderBy('position');

        return table_handler_paginate($filters, $query);
    }
}
