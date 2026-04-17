<?php

namespace Ingenius\Products\Actions\Variants;

use Illuminate\Support\Facades\DB;
use Ingenius\Products\Models\Product;
use Ingenius\Products\Models\ProductVariant;
use Ingenius\Products\Models\AttributeOption;
use Ingenius\Products\Services\VariantSkuGenerator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GenerateVariantsFromAttributesAction
{
    /**
     * Generate all variant combinations from the product's assigned attributes.
     *
     * @param Product $product
     * @param array $options Override defaults: regular_price, sale_price, handle_stock, stock, visible
     * @return array<ProductVariant>
     */
    public function __invoke(Product $product, array $options = []): array
    {
        return DB::transaction(function () use ($product, $options) {
            $requestedAttributeIds = $options['attribute_ids'] ?? null;
            $replaceExisting = (bool) ($options['replace_existing'] ?? false);

            // Handle conflict with existing variants when requested attributes differ
            if ($requestedAttributeIds !== null) {
                $existingAttributeIds = $this->getExistingVariantAttributeIds($product);

                if (!empty($existingAttributeIds)) {
                    $requestedSet = $this->normalizeIdSet($requestedAttributeIds);
                    $existingSet = $this->normalizeIdSet($existingAttributeIds);

                    if ($requestedSet !== $existingSet) {
                        if (!$replaceExisting) {
                            throw new HttpException(
                                409,
                                'Existing variants use different attributes. Set replace_existing=true to replace them.'
                            );
                        }

                        // Explicit consent: delete existing variants before regenerating
                        $product->variants()->delete();
                    }
                }

                // Sync product attributes inside the transaction
                $product->attributes()->sync($requestedAttributeIds);
                $product->load('attributes.options');
            }

            $attributes = $product->attributes()->with('options')->orderBy('position')->get();

            if ($attributes->isEmpty()) {
                return [];
            }

            // Collect option groups per attribute
            $optionGroups = $attributes->map(fn ($attr) => $attr->options->toArray())->toArray();

            // Generate cartesian product of all option combinations
            // e.g. Color[Red,Blue] x Size[S,M,L] = [Red/S, Red/M, Red/L, Blue/S, Blue/M, Blue/L]
            $combinations = $this->cartesianProduct($optionGroups);

            // Normalize excluded combinations for comparison
            $excludeCombinations = $options['exclude_combinations'] ?? [];
            $normalizedExclusions = array_map(function ($combo) {
                $sorted = array_map('intval', $combo);
                sort($sorted);
                return $sorted;
            }, $excludeCombinations);

            $generator = app(VariantSkuGenerator::class);
            $variants = [];
            $position = $product->variants()->max('position') ?? 0;

            foreach ($combinations as $combination) {
                $optionIds = array_column($combination, 'id');
                $optionNames = array_column($combination, 'name');

                // Skip excluded combinations
                $sortedIds = $optionIds;
                sort($sortedIds);
                if (in_array($sortedIds, $normalizedExclusions, true)) {
                    continue;
                }

                // Skip if this combination already exists
                if ($this->combinationExists($product, $optionIds)) {
                    continue;
                }

                $sku = $generator->generate($product, $optionNames);
                $position++;

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'regular_price' => $options['regular_price'] ?? null, // null = inherit
                    'sale_price' => $options['sale_price'] ?? null,
                    'handle_stock' => $options['handle_stock'] ?? true,
                    'stock' => $options['stock'] ?? 0,
                    'stock_for_sale' => $options['stock'] ?? 0,
                    'visible' => $options['visible'] ?? true,
                    'is_default' => false,
                    'position' => $position,
                ]);

                $variant->attributeOptions()->sync($optionIds);
                $variants[] = $variant->load('attributeOptions');
            }

            // If no default variant exists, set the first one
            if (!$product->variants()->where('is_default', true)->exists() && !empty($variants)) {
                $variants[0]->update(['is_default' => true]);
                $variants[0] = $variants[0]->fresh('attributeOptions');
            }

            return $variants;
        });
    }

    /**
     * Generate the cartesian product of multiple option groups.
     *
     * Given [[Red, Blue], [S, M, L]], produces:
     * [[Red, S], [Red, M], [Red, L], [Blue, S], [Blue, M], [Blue, L]]
     */
    private function cartesianProduct(array $groups): array
    {
        if (empty($groups)) {
            return [];
        }

        $result = [[]];

        foreach ($groups as $group) {
            $newResult = [];
            foreach ($result as $existing) {
                foreach ($group as $option) {
                    $newResult[] = array_merge($existing, [$option]);
                }
            }
            $result = $newResult;
        }

        return $result;
    }

    /**
     * Check if a variant with the exact same attribute option combination already exists.
     */
    private function combinationExists(Product $product, array $optionIds): bool
    {
        sort($optionIds);

        foreach ($product->variants()->with('attributeOptions')->get() as $variant) {
            $existingIds = $variant->attributeOptions->pluck('id')->sort()->values()->toArray();
            if ($existingIds === $optionIds) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the unique attribute IDs used by the product's existing variants.
     */
    private function getExistingVariantAttributeIds(Product $product): array
    {
        return $product->variants()
            ->with('attributeOptions.attribute')
            ->get()
            ->flatMap(fn ($variant) => $variant->attributeOptions->pluck('attribute_id'))
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Normalize an array of IDs into a sorted, unique, integer-keyed set for comparison.
     */
    private function normalizeIdSet(array $ids): array
    {
        $normalized = array_values(array_unique(array_map('intval', $ids)));
        sort($normalized);
        return $normalized;
    }
}
