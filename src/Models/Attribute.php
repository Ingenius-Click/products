<?php

namespace Ingenius\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Attribute extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'position',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class)->orderBy('position');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attributes', 'attribute_id', 'product_id');
    }
}
