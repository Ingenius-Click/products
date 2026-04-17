<?php

namespace Ingenius\Products\Services;

use Ingenius\Products\Models\Product;
use Ingenius\Products\Models\ProductVariant;

class VariantSkuGenerator
{
    /**
     * Generate a SKU for a variant based on the parent product SKU and attribute options.
     */
    public function generate(Product $product, array $attributeOptionNames = []): string
    {
        $baseSku = $product->sku;

        if (!empty($attributeOptionNames)) {
            $suffix = collect($attributeOptionNames)
                ->map(fn (string $name) => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3)))
                ->implode('-');

            $sku = $baseSku . '-' . $suffix;
        } else {
            $sku = $baseSku . '-V';
        }

        return $this->ensureUnique($sku);
    }

    /**
     * Generate a sequential variant SKU for a product.
     */
    public function generateSequential(Product $product): string
    {
        $baseSku = $product->sku;
        $nextNumber = $this->getNextVariantNumber($baseSku);

        $sku = $baseSku . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return $this->ensureUnique($sku);
    }

    /**
     * Ensure SKU is unique across both products and variants.
     */
    private function ensureUnique(string $sku): string
    {
        $original = $sku;
        $counter = 1;
        $maxAttempts = 100;

        while ($counter <= $maxAttempts) {
            $existsInProducts = Product::withTrashed()->where('sku', $sku)->exists();
            $existsInVariants = ProductVariant::withTrashed()->where('sku', $sku)->exists();

            if (!$existsInProducts && !$existsInVariants) {
                return $sku;
            }

            $sku = $original . '-' . $counter;
            $counter++;
        }

        throw new \RuntimeException('Unable to generate unique variant SKU after ' . $maxAttempts . ' attempts');
    }

    private function getNextVariantNumber(string $baseSku): int
    {
        $lastVariant = ProductVariant::withTrashed()
            ->where('sku', 'LIKE', $baseSku . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastVariant) {
            return 1;
        }

        $suffix = substr($lastVariant->sku, strlen($baseSku) + 1);
        $numericPart = (int) preg_replace('/[^0-9]/', '', $suffix);

        return $numericPart + 1;
    }
}
