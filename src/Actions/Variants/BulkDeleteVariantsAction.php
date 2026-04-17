<?php

namespace Ingenius\Products\Actions\Variants;

class BulkDeleteVariantsAction
{
    public function __invoke($product, array $variantIds): void
    {
        $product->variants()->whereIn('id', $variantIds)->delete();
    }
}