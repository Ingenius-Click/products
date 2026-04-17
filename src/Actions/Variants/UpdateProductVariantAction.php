<?php

namespace Ingenius\Products\Actions\Variants;

use Illuminate\Support\Facades\DB;
use Ingenius\Core\Traits\HandleImages;
use Ingenius\Products\Models\ProductVariant;

class UpdateProductVariantAction
{
    use HandleImages;

    public function __invoke(ProductVariant $variant, array $data): ProductVariant
    {
        return DB::transaction(function () use ($variant, $data) {
            // Handle price conversion from normal format
            if (isset($data['normal_regular_price'])) {
                $data['regular_price'] = $data['normal_regular_price'] * 100;
            }
            if (isset($data['normal_sale_price'])) {
                $data['sale_price'] = $data['normal_sale_price'] * 100;
            }

            if (!isset($data['handle_stock']) || !$data['handle_stock']) {
                unset($data['stock']);
                unset($data['stock_for_sale']);
            }

            // If this is set as default, unset other defaults
            if (!empty($data['is_default'])) {
                $variant->product->variants()
                    ->where('id', '!=', $variant->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $variant->update($data);

            // Sync attribute options
            if (isset($data['attribute_option_ids'])) {
                $variant->attributeOptions()->sync($data['attribute_option_ids']);
            }

            // Handle images
            if (isset($data['new_images'])) {
                $this->saveImages($data['new_images'], $variant);
            }

            if (isset($data['removed_images'])) {
                $this->removeImages($data['removed_images'], $variant);
            }

            return $variant->fresh('attributeOptions');
        });
    }
}
