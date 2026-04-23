<?php

namespace Ingenius\Products\Models;

use Ingenius\Core\Support\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ingenius\Core\Interfaces\IInventoriable;
use Ingenius\Core\Interfaces\IPurchasable;
use Ingenius\Core\Services\PackageHookManager;
use Ingenius\Products\Services\ProductPriceCacheService;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariant extends Model implements IPurchasable, IInventoriable, HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'regular_price',
        'sale_price',
        'handle_stock',
        'stock',
        'stock_for_sale',
        'is_default',
        'visible',
        'position',
    ];

    protected $appends = [
        'images',
        'attribute_options_ids',
        'name'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'visible' => 'boolean',
        'handle_stock' => 'boolean',
    ];

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeOptions(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeOption::class,
            'product_variant_attribute_options',
            'product_variant_id',
            'attribute_option_id'
        );
    }

    // ──────────────────────────────────────────────
    // IPurchasable - with inheritance from parent
    // ──────────────────────────────────────────────

    public function getFinalPrice(): int
    {
        $cacheService = app(ProductPriceCacheService::class);
        $cached = $cacheService->getFinalPrice($this->id, get_class($this));

        if ($cached !== null) {
            return $cached;
        }

        $basePrice = $this->getEffectiveSalePrice();


        $hookManager = app(PackageHookManager::class);

        $finalPrice = $hookManager->execute('product.final_price', $basePrice, [
            'product_id' => $this->id,
            'product_class' => get_class($this),
            'parent_product_id' => $this->product_id,
            'base_price' => $basePrice,
            'regular_price' => $this->getEffectiveRegularPrice(),
        ]);

        $cacheService->setFinalPrice($this->id, get_class($this), $finalPrice);

        return $finalPrice;
    }

    public function getShowcasePrice(): int
    {
        $cacheService = app(ProductPriceCacheService::class);
        $cached = $cacheService->getShowcasePrice($this->id, get_class($this));

        if ($cached !== null) {
            return $cached;
        }

        $basePrice = $this->getEffectiveSalePrice();

        $hookManager = app(PackageHookManager::class);

        $showcasePrice = $hookManager->execute('product.showcase_price', $basePrice, [
            'product_id' => $this->id,
            'product_class' => get_class($this),
            'parent_product_id' => $this->product_id,
            'base_price' => $basePrice,
            'regular_price' => $this->getEffectiveRegularPrice(),
        ]);

        $cacheService->setShowcasePrice($this->id, get_class($this), $showcasePrice);

        return $showcasePrice;
    }

    public function getRegularPrice(): int
    {
        return $this->getEffectiveRegularPrice();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        $optionNames = $this->attributeOptions->pluck('name')->implode(' / ');

        return $this->product->name . ($optionNames ? " - {$optionNames}" : '');
    }

    public function getNameAttribute(): string 
    {
        return $this->getName();
    }

    public function canBePurchased(): bool
    {
        if (!$this->getAttribute('visible')) {
            return false;
        }

        return $this->product->isBasePurchasable();
    }

    // ──────────────────────────────────────────────
    // IInventoriable - with delegation to parent
    // ──────────────────────────────────────────────

    public function getStock(): ?float
    {
        if (!$this->handle_stock) {
            return $this->product->getStock();
        }

        return $this->stock_for_sale;
    }

    public function addStock(float $amount): void
    {
        if (!$this->handle_stock) {
            $this->product->addStock($amount);
            return;
        }

        $this->stock += $amount;
        $this->stock_for_sale += $amount;
        $this->save();
    }

    public function removeStock(float $amount): void
    {
        if (!$this->handle_stock) {
            $this->product->removeStock($amount);
            return;
        }

        $this->stock -= $amount;
        $this->stock_for_sale -= $amount;

        if ($this->stock_for_sale < 0) {
            $this->stock_for_sale = 0;
        }

        if ($this->stock < 0) {
            $this->stock = 0;
        }

        $this->save();
    }

    public function inStock(): bool
    {
        if (!$this->handle_stock) {
            return $this->product->inStock();
        }

        return $this->stock_for_sale > 0;
    }

    public function handleStock(): bool
    {
        if (!$this->handle_stock) {
            return $this->product->handleStock();
        }

        return true;
    }

    public function hasEnoughStock(float $quantity): bool
    {
        if (!$this->handle_stock) {
            return $this->product->hasEnoughStock($quantity);
        }

        if ($this->stock_for_sale === null) {
            return true;
        }

        return $this->stock_for_sale >= $quantity;
    }

    // ──────────────────────────────────────────────
    // Price inheritance helpers
    // ──────────────────────────────────────────────

    /**
     * Get effective regular price: own price or inherited from parent product.
     */
    public function getEffectiveRegularPrice(): int
    {
        return $this->regular_price ?? $this->product->regular_price;
    }

    /**
     * Get effective sale price: own price or inherited from parent product.
     */
    public function getEffectiveSalePrice(): int
    {
        return $this->sale_price ?? $this->regular_price ?? $this->product->sale_price;
    }

    // ──────────────────────────────────────────────
    // Media
    // ──────────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(235)
                    ->height(235)
                    ->nonQueued();
                $this->addMediaConversion('rectangle')
                    ->width(365)
                    ->height(190)
                    ->nonQueued();
            });
    }

    /**
     * @return array<Image>
     */
    protected function getImagesAttribute(): array
    {
        $collection = $this->getMedia('images');

        // If variant has no images, fall back to parent product images
        // if ($collection->isEmpty()) {
        //     return $this->product->images;
        // }

        $images = [];

        foreach ($collection as $media) {
            $images[] = new Image(
                $media->id,
                $media->getUrl(),
                $media->getUrl('thumb'),
                $media->getUrl('rectangle'),
                $media->mime_type,
                $media->size
            );
        }

        return $images;
    }

    public function getAttributeOptionsIdsAttribute(): array
    {
        return $this->attributeOptions()->pluck('attribute_options.id')->toArray();
    }
}
