<?php

namespace Ingenius\Products\Actions\Variants;

use Ingenius\Products\Models\Attribute;
use Ingenius\Products\Models\Product;
use Ingenius\Products\Services\VariantSkuGenerator;

class PreviewVariantsFromAttributesAction
{
    /**
     * Generate a preview of all variant combinations without persisting anything.
     *
     * @param Product $product
     * @param array $attributeIds
     * @return array
     */
    public function __invoke(Product $product, array $attributeIds, array $excludeCombinations = []): array
    {
        $attributes = Attribute::with(['options' => fn ($q) => $q->orderBy('position')])
            ->whereIn('id', $attributeIds)
            ->orderBy('position')
            ->get();

        if ($attributes->isEmpty()) {
            return [];
        }

        $optionGroups = $attributes->map(fn ($attr) => $attr->options->toArray())->toArray();
        $combinations = $this->cartesianProduct($optionGroups);

        // Normalize excluded combinations for comparison
        $normalizedExclusions = array_map(function ($combo) {
            $sorted = array_map('intval', $combo);
            sort($sorted);
            return $sorted;
        }, $excludeCombinations);

        $generator = app(VariantSkuGenerator::class);
        $existingCombinations = $this->getExistingCombinations($product);
        $preview = [];

        foreach ($combinations as $combination) {
            $optionIds = array_column($combination, 'id');
            sort($optionIds);

            // Skip excluded combinations
            if (in_array($optionIds, $normalizedExclusions, true)) {
                continue;
            }

            $optionNames = array_column($combination, 'name');
            $sku = $generator->generate($product, $optionNames);

            $preview[] = [
                'sku' => $sku,
                'attribute_options' => collect($combination)->map(fn ($opt) => [
                    'id' => $opt['id'],
                    'name' => $opt['name'],
                    'value' => $opt['value'] ?? null,
                    'attribute_id' => $opt['attribute_id'],
                ]),
                'already_exists' => in_array($optionIds, $existingCombinations, true),
            ];
        }

        return $preview;
    }

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
     * Get all existing variant option combinations for the product.
     */
    private function getExistingCombinations(Product $product): array
    {
        return $product->variants()
            ->with('attributeOptions')
            ->get()
            ->map(fn ($v) => $v->attributeOptions->pluck('id')->sort()->values()->toArray())
            ->toArray();
    }
}
