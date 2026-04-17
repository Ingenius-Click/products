<?php

namespace Ingenius\Products\Actions\Variants;

use Illuminate\Support\Facades\DB;
use Ingenius\Products\Models\ProductVariant;

class DeleteProductVariantAction
{
    public function __invoke(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {
            // If deleting the default variant, promote the next one
            if ($variant->is_default) {
                $nextVariant = $variant->product->variants()
                    ->where('id', '!=', $variant->id)
                    ->where('visible', true)
                    ->orderBy('position')
                    ->first();

                if ($nextVariant) {
                    $nextVariant->update(['is_default' => true]);
                }
            }

            $variant->delete();
        });
    }
}
