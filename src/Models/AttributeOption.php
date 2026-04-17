<?php

namespace Ingenius\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AttributeOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'name',
        'value',
        'position',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_attribute_options',
            'attribute_option_id',
            'product_variant_id'
        );
    }
}
