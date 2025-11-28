<?php

namespace Ingenius\Products\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Ingenius\Products\Services\ProductPriceCacheService;

/**
 * Debug controller for price cache statistics
 *
 * This controller provides endpoints to monitor cache performance
 * and query optimization. Should only be enabled in development/staging.
 */
class PriceCacheDebugController extends Controller
{
    public function __construct(
        protected ProductPriceCacheService $priceCache
    ) {}

    /**
     * Get basic cache statistics
     *
     * GET /api/debug/price-cache/stats
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->priceCache->getStats(),
        ]);
    }

    /**
     * Get detailed statistics with query logging
     *
     * GET /api/debug/price-cache/detailed-stats
     */
    public function detailedStats(): JsonResponse
    {
        // Enable query logging for this request
        DB::enableQueryLog();

        $stats = $this->priceCache->getDetailedStats(true);

        // Get all queries executed so far
        $queries = DB::getQueryLog();

        return response()->json([
            'success' => true,
            'data' => [
                'cache_stats' => $stats,
                'query_count' => count($queries),
                'queries' => $queries,
            ],
        ]);
    }

    /**
     * Reset cache statistics
     *
     * POST /api/debug/price-cache/reset-stats
     */
    public function resetStats(): JsonResponse
    {
        $oldStats = $this->priceCache->getStats();
        $this->priceCache->resetStats();

        return response()->json([
            'success' => true,
            'message' => 'Cache statistics reset successfully',
            'previous_stats' => $oldStats,
            'current_stats' => $this->priceCache->getStats(),
        ]);
    }

    /**
     * Clear all cached prices
     *
     * POST /api/debug/price-cache/clear
     */
    public function clear(): JsonResponse
    {
        $oldStats = $this->priceCache->getStats();
        $this->priceCache->clear();

        return response()->json([
            'success' => true,
            'message' => 'Price cache cleared successfully',
            'previous_stats' => $oldStats,
        ]);
    }

    /**
     * Test cache performance with query counting
     *
     * GET /api/debug/price-cache/test-performance
     */
    public function testPerformance(): JsonResponse
    {
        // Clear previous stats
        $this->priceCache->resetStats();
        DB::flushQueryLog();
        DB::enableQueryLog();

        $productModel = config('storefront.product_model', config('products.product_model'));

        if (!$productModel) {
            return response()->json([
                'success' => false,
                'error' => 'Product model not configured',
            ], 500);
        }

        // Get 10 products for testing
        $products = $productModel::limit(10)->get();

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No products found for testing',
            ], 404);
        }

        // Test 1: Without cache warming (cold)
        $this->priceCache->clear();
        DB::flushQueryLog();

        $startTime = microtime(true);
        foreach ($products as $product) {
            $product->getFinalPrice();
            $product->getShowcasePrice();
        }
        $coldTime = microtime(true) - $startTime;
        $coldQueries = DB::getQueryLog();
        $coldStats = $this->priceCache->getStats();

        // Test 2: With cache warming (warm)
        $this->priceCache->clear();
        DB::flushQueryLog();

        $startTime = microtime(true);
        $this->priceCache->warmBulkPrices($products->all());
        foreach ($products as $product) {
            $product->getFinalPrice();
            $product->getShowcasePrice();
        }
        $warmTime = microtime(true) - $startTime;
        $warmQueries = DB::getQueryLog();
        $warmStats = $this->priceCache->getStats();

        return response()->json([
            'success' => true,
            'test_configuration' => [
                'product_count' => $products->count(),
                'product_class' => get_class($products->first()),
            ],
            'cold_start' => [
                'execution_time_ms' => round($coldTime * 1000, 2),
                'query_count' => count($coldQueries),
                'cache_stats' => $coldStats,
            ],
            'warm_start' => [
                'execution_time_ms' => round($warmTime * 1000, 2),
                'query_count' => count($warmQueries),
                'cache_stats' => $warmStats,
            ],
            'improvement' => [
                'time_saved_ms' => round(($coldTime - $warmTime) * 1000, 2),
                'time_saved_percentage' => $coldTime > 0 ? round((($coldTime - $warmTime) / $coldTime) * 100, 2) : 0,
                'queries_saved' => count($coldQueries) - count($warmQueries),
                'queries_saved_percentage' => count($coldQueries) > 0 ? round(((count($coldQueries) - count($warmQueries)) / count($coldQueries)) * 100, 2) : 0,
            ],
        ]);
    }
}
