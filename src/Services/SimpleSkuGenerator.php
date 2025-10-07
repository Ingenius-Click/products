<?php

namespace Ingenius\Products\Services;

use Ingenius\Products\Models\Product;
use Ingenius\Products\Settings\ProductSettings;

class SimpleSkuGenerator implements \Ingenius\Products\Interfaces\ISkuGeneratorImplementation
{
    public function generateSku(): string
    {
        $productSettings = app(ProductSettings::class);

        $prefix = $productSettings->sku_prefix;
        $maxAttempts = 100;
        $attempt = 0;

        do {
            $nextNumber = $this->getNextSequentialNumber($prefix);
            $sku = $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $attempt++;

            // Check if SKU already exists (including soft deleted products)
            $exists = Product::withTrashed()->where('sku', $sku)->exists();

            if (!$exists) {
                return $sku;
            }
        } while ($attempt < $maxAttempts);

        // Fallback: if we somehow can't find a unique SKU, throw an exception
        throw new \RuntimeException('Unable to generate unique SKU after ' . $maxAttempts . ' attempts');
    }

    /**
     * Get the next sequential number based on existing SKUs
     */
    private function getNextSequentialNumber(string $prefix): int
    {
        // Get the highest SKU number with the given prefix
        $lastProduct = Product::withTrashed()
            ->where('sku', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(sku, ' . (strlen($prefix) + 1) . ') AS INTEGER) DESC')
            ->first();

        if (!$lastProduct) {
            return 1;
        }

        // Extract the numeric part from the SKU
        $lastSku = $lastProduct->sku;
        $numericPart = substr($lastSku, strlen($prefix));
        $lastNumber = (int) $numericPart;

        return $lastNumber + 1;
    }
}