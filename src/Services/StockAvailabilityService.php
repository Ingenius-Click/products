<?php

namespace Ingenius\Products\Services;

use Illuminate\Support\Facades\Cache;
use Ingenius\Core\Interfaces\IInventoriable;
use Ingenius\Core\Interfaces\StockAvailabilityInterface;
use Ingenius\Core\Services\PackageHookManager;

class StockAvailabilityService implements StockAvailabilityInterface
{
    private int $cacheHits = 0;
    private int $cacheMisses = 0;
    private int $invalidations = 0;

    public function __construct(
        protected PackageHookManager $hookManager
    ) {}

    /**
     * Get the real available stock for a product, accounting for reservations
     * in carts and pending orders (collected via hooks from other packages).
     *
     * Returns null if the product doesn't manage stock or has unlimited stock.
     *
     * Pass $context with exclusion keys to skip specific reservations:
     *   - exclude_cart_owner_id + exclude_cart_owner_type: skip authenticated user's cart
     *   - exclude_cart_guest_token: skip guest's cart
     *
     * When $context is non-empty the result is NOT cached (query is run fresh).
     */
    public function getAvailableStock(IInventoriable $product, array $context = []): ?float
    {
        if (!$product->handleStock()) {
            return null;
        }

        $stock = $product->getStock();

        if ($stock === null) {
            return null;
        }

        $productId = $product->id;
        $productType = get_class($product);

        $hookContext = array_merge([
            'productible_id' => $productId,
            'productible_type' => $productType,
        ], $context);

        // When custom exclusion context is provided, skip cache to get an accurate
        // real-time count (e.g. during order creation where user's own cart must be excluded).
        if (!empty($context)) {
            $this->cacheMisses++;
            $totalReserved = $this->hookManager->execute('stock.reservations.get', 0, $hookContext);
            return max(0, $stock - $totalReserved);
        }

        $cacheKey = $this->buildCacheKey($productType, $productId);
        $ttl = config('products.stock_cache_ttl', 300);

        $isHit = Cache::has($cacheKey);

        $result = Cache::remember($cacheKey, $ttl, function () use ($stock, $hookContext) {
            $totalReserved = $this->hookManager->execute('stock.reservations.get', 0, $hookContext);

            return max(0, $stock - $totalReserved);
        });

        if ($isHit) {
            $this->cacheHits++;
        } else {
            $this->cacheMisses++;
        }

        return $result;
    }

    /**
     * Check if a product has enough available stock for the requested quantity.
     *
     * @see getAvailableStock for supported $context keys.
     */
    public function hasAvailableStock(IInventoriable $product, float $quantity, array $context = []): bool
    {
        $available = $this->getAvailableStock($product, $context);

        return $available === null || $available >= $quantity;
    }

    /**
     * Invalidate the cached available stock for a specific product.
     * Should be called whenever cart items or orders change.
     */
    public function invalidateCache(string $productibleType, int $productibleId): void
    {
        Cache::forget($this->buildCacheKey($productibleType, $productibleId));
        $this->invalidations++;
    }

    /**
     * Get cache hit/miss statistics for the current request lifecycle.
     */
    public function getStats(): array
    {
        $total = $this->cacheHits + $this->cacheMisses;
        $hitRate = $total > 0 ? round(($this->cacheHits / $total) * 100, 2) : 0;

        return [
            'cache_hits'           => $this->cacheHits,
            'cache_misses'         => $this->cacheMisses,
            'total_calls'          => $total,
            'hit_rate_percentage'  => $hitRate,
            'invalidations'        => $this->invalidations,
            'db_queries_avoided'   => $this->cacheHits,
        ];
    }

    protected function buildCacheKey(string $productibleType, int $productibleId): string
    {
        $tenant = tenant();
        $tenantId = $tenant ? $tenant->id : 'central';

        return "stock_avail:{$tenantId}:{$productibleType}:{$productibleId}";
    }
}
