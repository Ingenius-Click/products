<?php

namespace Ingenius\Products\Models;

use Ingenius\Core\Support\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

use Ingenius\Products\Database\Factories\CategoryFactory;

class Category extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    protected $appends = [
        'images',
    ];

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

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
     * @return array<Image>
     */
    protected function getImagesAttribute(): array
    {
        $collection = $this->getMedia('category_images');

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
        $this->addMediaCollection('category_images')
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
     * Get the parent category if any
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the direct children categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Check if this category is a descendant of the given category
     *
     * @param Category|int $category
     * @return bool
     */
    public function isDescendantOf($category): bool
    {
        $categoryId = $category instanceof Category ? $category->id : $category;

        // If this category has no parent, it can't be a descendant of any category
        if (!$this->parent_id) {
            return false;
        }

        // If this category's parent is the given category, it is a direct descendant
        if ($this->parent_id == $categoryId) {
            return true;
        }

        // Recursively check if this category's parent is a descendant of the given category
        return $this->parent ? $this->parent->isDescendantOf($categoryId) : false;
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'products_categories', 'category_id', 'product_id');
    }
}
