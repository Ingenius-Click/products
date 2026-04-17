<?php

namespace Ingenius\Products\Actions\Variants;

use Illuminate\Support\Facades\DB;
use Ingenius\Products\Models\ProductVariant;

class SetDefaultVariantAction
{
    public function __invoke(ProductVariant $variant): ProductVariant
    {
        return DB::transaction(function () use ($variant) {
            // Unset all other defaults for this product
            $variant->product->variants()
                ->where('id', '!=', $variant->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);

            $variant->update(['is_default' => true]);

            return $variant->fresh();
        });
    }
}
