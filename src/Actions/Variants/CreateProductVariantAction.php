<?php

namespace Ingenius\Products\Actions\Variants;

use Illuminate\Support\Facades\DB;
use Ingenius\Core\Traits\HandleImages;
use Ingenius\Products\Models\Product;
use Ingenius\Products\Models\ProductVariant;
use Ingenius\Products\Services\VariantSkuGenerator;

class CreateProductVariantAction
{
    use HandleImages;

    public function __invoke(Product $product, array $data): ProductVariant
    {
        return DB::transaction(function () use ($product, $data) {
            // Handle price conversion from normal format
            if (isset($data['normal_regular_price'])) {
                $data['regular_price'] = $data['normal_regular_price'] * 100;
            }
            if (isset($data['normal_sale_price'])) {
                $data['sale_price'] = $data['normal_sale_price'] * 100;
            }

            // Generate SKU if not provided
            if (empty($data['sku'])) {
                $generator = app(VariantSkuGenerator::class);
                $optionNames = [];

                if (isset($data['attribute_option_ids'])) {
                    $optionNames = \Ingenius\Products\Models\AttributeOption::whereIn('id', $data['attribute_option_ids'])
                        ->pluck('name')
                        ->toArray();
                }

                $data['sku'] = $generator->generate($product, $optionNames);
            }

            if (!isset($data['handle_stock']) || !$data['handle_stock']) {
                unset($data['stock']);
                unset($data['stock_for_sale']);
            }

            $data['product_id'] = $product->id;

            // If this is set as default, unset other defaults
            if (!empty($data['is_default'])) {
                $product->variants()->where('is_default', true)->update(['is_default' => false]);
            }

            $variant = ProductVariant::create($data);

            // Attach attribute options
            if (isset($data['attribute_option_ids'])) {
                $variant->attributeOptions()->sync($data['attribute_option_ids']);
            }

            // Handle images
            if (isset($data['new_images'])) {
                $this->saveImages($data['new_images'], $variant);
            }

            return $variant->fresh('attributeOptions');
        });
    }
}
