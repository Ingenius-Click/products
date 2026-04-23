<?php

namespace Ingenius\Products\Actions\Variants;

use Illuminate\Support\Facades\DB;
use Ingenius\Products\Models\Product;
use Ingenius\Products\Models\ProductVariant;

class BulkUpdateVariantsAction
{
    /**
     * Bulk update stock and/or prices for variants of a product.
     */
    public function __invoke(Product $product, array $variants): array
    {
        return DB::transaction(function () use ($product, $variants) {
            $updated = [];

            foreach ($variants as $variantData) {
                $variant = $product->variants()->find($variantData['id']);

                if (!$variant) {
                    continue;
                }

                $updateData = [];

                if (isset($variantData['stock'])) {
                    $updateData['stock'] = $variantData['stock'];
                    $updateData['stock_for_sale'] = $variantData['stock'];
                }

                if (isset($variantData['normal_regular_price'])) {
                    $updateData['regular_price'] = $variantData['normal_regular_price'] * 100;
                }

                if (isset($variantData['normal_sale_price'])) {
                    $updateData['sale_price'] = $variantData['normal_sale_price'] * 100;
                }

                if (isset($variantData['visible'])) {
                    $updateData['visible'] = $variantData['visible'];
                }

                    if (isset($variantData['is_default'])) {
                        if ($variantData['is_default']) {
                            // Unset is_default for all other variants of the product
                            $product->variants()->where('id', '!=', $variant->id)->update(['is_default' => false]);
                            $updateData['is_default'] = $variantData['is_default'];
                        }
                    }

                if (!empty($updateData)) {
                    $variant->update($updateData);
                    $updated[] = $variant->fresh();
                }
            }

            return $updated;
        });
    }
}
