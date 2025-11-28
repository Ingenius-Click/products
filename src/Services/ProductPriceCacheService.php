<?php

namespace Ingenius\Products\Services;

use Ingenius\Core\Services\PackageHookManager;

/**
 * Request-scoped cache for product prices
 *
 * This service caches calculated product prices (both final and showcase)
 * during the request lifecycle to avoid redundant price calculations.
 * It supports both individual price caching and bulk price warming.
 */
class ProductPriceCacheService
{
    /**
     * Cache for final prices
     * Format: ['ProductClass:product_id' => price_in_cents]
     */
    private array $finalPrices = [];

    /**
     * Cache for showcase prices
     * Format: ['ProductClass:product_id' => price_in_cents]
     */
    private array $showcasePrices = [];

    /**
     * Statistics tracking
     */
    private int $cacheHits = 0;
    private int $cacheMisses = 0;
    private int $hookExecutions = 0;
    private int $bulkOperations = 0;

    public function __construct(
        protected PackageHookManager $hookManager
    ) {}

    /**
     * Get cached final price for a product
     *
     * @param int $productId
     * @param string $productClass
     * @return int|null Cached price or null if not cached
     */
    public function getFinalPrice(int $productId, string $productClass): ?int
    {
        $key = "{$productClass}:{$productId}";
        $cached = $this->finalPrices[$key] ?? null;

        if ($cached !== null) {
            $this->cacheHits++;
        } else {
            $this->cacheMisses++;
        }

        return $cached;
    }

    /**
     * Get cached showcase price for a product
     *
     * @param int $productId
     * @param string $productClass
     * @return int|null Cached price or null if not cached
     */
    public function getShowcasePrice(int $productId, string $productClass): ?int
    {
        $key = "{$productClass}:{$productId}";
        $cached = $this->showcasePrices[$key] ?? null;

        if ($cached !== null) {
            $this->cacheHits++;
        } else {
            $this->cacheMisses++;
        }

        return $cached;
    }

    /**
     * Set final price in cache
     *
     * @param int $productId
     * @param string $productClass
     * @param int $price Price in cents
     */
    public function setFinalPrice(int $productId, string $productClass, int $price): void
    {
        $key = "{$productClass}:{$productId}";
        $this->finalPrices[$key] = $price;
        $this->hookExecutions++;
    }

    /**
     * Set showcase price in cache
     *
     * @param int $productId
     * @param string $productClass
     * @param int $price Price in cents
     */
    public function setShowcasePrice(int $productId, string $productClass, int $price): void
    {
        $key = "{$productClass}:{$productId}";
        $this->showcasePrices[$key] = $price;
        $this->hookExecutions++;
    }

    /**
     * Clear all cached prices
     */
    public function clear(): void
    {
        $this->finalPrices = [];
        $this->showcasePrices = [];
        $this->cacheHits = 0;
        $this->cacheMisses = 0;
        $this->hookExecutions = 0;
        $this->bulkOperations = 0;
    }

    /**
     * Check if prices are cached for a product
     *
     * @param int $productId
     * @param string $productClass
     * @return bool
     */
    public function hasCachedPrices(int $productId, string $productClass): bool
    {
        $key = "{$productClass}:{$productId}";
        return isset($this->finalPrices[$key]) && isset($this->showcasePrices[$key]);
    }

    /**
     * Warm cache for multiple products at once using bulk price calculation
     *
     * This method triggers the 'product.bulk.prices' hook which allows
     * packages (like discounts) to calculate prices for multiple products
     * in a single operation, reducing database queries.
     *
     * @param array $products Array of product models or collections
     * @return void
     */
    public function warmBulkPrices(array $products): void
    {
        if (empty($products)) {
            return;
        }

        // Prepare product data for bulk processing
        $productData = [];
        foreach ($products as $product) {
            // Skip if already cached
            if ($this->hasCachedPrices($product->id, get_class($product))) {
                continue;
            }

            $productData[] = [
                'product_id' => $product->id,
                'product_class' => get_class($product),
                'base_price' => $product->sale_price,
                'regular_price' => $product->regular_price,
            ];
        }

        // If all products were already cached, return early
        if (empty($productData)) {
            return;
        }

        // Execute hook - returns ['ProductClass:product_id' => ['final' => int, 'showcase' => int]]
        $bulkPrices = $this->hookManager->execute('product.bulk.prices', [], [
            'products' => $productData
        ]);

        // Store calculated prices in cache
        foreach ($bulkPrices as $key => $prices) {
            $this->finalPrices[$key] = $prices['final'];
            $this->showcasePrices[$key] = $prices['showcase'];
        }

        $this->bulkOperations++;
    }

    /**
     * Get cache statistics for debugging
     *
     * @return array
     */
    public function getStats(): array
    {
        $totalRequests = $this->cacheHits + $this->cacheMisses;
        $hitRate = $totalRequests > 0 ? ($this->cacheHits / $totalRequests) * 100 : 0;

        return [
            'cache_hits' => $this->cacheHits,
            'cache_misses' => $this->cacheMisses,
            'total_requests' => $totalRequests,
            'hit_rate_percentage' => round($hitRate, 2),
            'hook_executions' => $this->hookExecutions,
            'bulk_operations' => $this->bulkOperations,
            'final_prices_cached' => count($this->finalPrices),
            'showcase_prices_cached' => count($this->showcasePrices),
            'total_products_cached' => count(array_unique(array_merge(
                array_keys($this->finalPrices),
                array_keys($this->showcasePrices)
            ))),
            'estimated_queries_saved' => max(0, $this->cacheHits - $this->bulkOperations),
        ];
    }

    /**
     * Get detailed statistics with query count tracking
     * This method integrates with Laravel's query log if enabled
     *
     * @param bool $enableQueryLog Whether to enable Laravel's query log
     * @return array
     */
    public function getDetailedStats(bool $enableQueryLog = false): array
    {
        $stats = $this->getStats();

        if ($enableQueryLog && \DB::getQueryLog() !== null) {
            $queries = \DB::getQueryLog();
            $stats['total_db_queries'] = count($queries);
            $stats['discount_related_queries'] = count(array_filter($queries, function($query) {
                return stripos($query['query'] ?? '', 'discount') !== false;
            }));
        }

        return $stats;
    }

    /**
     * Reset statistics (useful for testing)
     */
    public function resetStats(): void
    {
        $this->cacheHits = 0;
        $this->cacheMisses = 0;
        $this->hookExecutions = 0;
        $this->bulkOperations = 0;
    }
}
