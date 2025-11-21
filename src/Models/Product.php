<?php

namespace Ingenius\Products\Models;

use Ingenius\Core\Support\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ingenius\Products\Database\Factories\ProductFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Ingenius\Core\Interfaces\IBaseProductibleData;
use Ingenius\Core\Interfaces\IInventoriable;
use Ingenius\Core\Interfaces\IPurchasable;
use Ingenius\Products\Traits\HasBaseProductibleData;
use Ingenius\Products\Traits\HasPurchasableData;
use Ingenius\Products\Traits\HasInventoriableData;

class Product extends Model implements IBaseProductibleData, IPurchasable, IInventoriable, HasMedia
{
    use HasFactory, InteractsWithMedia, HasSlug, SoftDeletes, HasBaseProductibleData, HasPurchasableData, HasInventoriableData;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'visible',
        'regular_price',
        'sale_price',
        'handle_stock',
        'stock',
        'stock_for_sale',
        'unit_of_measurement',
        'short_description'
    ];

    protected $appends = [
        'images',
        'categories_ids'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        // Try to get config from main app first, fall back to module config
        $options = Config::get('sluggable.options', Config::get('products.sluggable.options', []));

        $slugOptions = SlugOptions::create()
            ->generateSlugsFrom($options['source'] ?? 'name')
            ->saveSlugsTo($options['destination'] ?? 'slug');

        if (isset($options['max_length'])) {
            $slugOptions->slugsShouldBeNoLongerThan($options['max_length']);
        }

        if (isset($options['separator'])) {
            $slugOptions->usingSeparator($options['separator']);
        }

        if (isset($options['language'])) {
            $slugOptions->usingLanguage($options['language']);
        }

        if (isset($options['unique']) && $options['unique'] === false) {
            $slugOptions->allowDuplicateSlugs();
        }

        return $slugOptions;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    /**
     * @return array<Image>
     */
    protected function getImagesAttribute(): array
    {
        $collection = $this->getMedia('images');

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
        $this->addMediaCollection('file')
            ->singleFile();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'products_categories', 'product_id', 'category_id');
    }

    public function getCategoriesIdsAttribute(): array
    {
        return $this->categories()->get()->pluck('id')->toArray();
    }

    public function scopeReadyForSale(Builder $query): Builder
    {
        return $query->where('visible', true)
            ->where(function ($query) {
                $query->where('handle_stock', false)
                    ->orWhere(function ($query) {
                        $query->where('handle_stock', true)
                            ->where('stock_for_sale', '>', 0);
                    });
            });
    }
}
