<?php

return [
    /*
     * The given function generates a URL friendly "slug" from the tag name property before saving it.
     * Defaults to Str::slug (https://laravel.com/docs/master/helpers#method-str-slug)
     */
    'slugger' => null,

    /*
     * The fully qualified class name of the slug model.
     */
    'slug_model' => Ingenius\Products\Models\Product::class,

    /*
     * Product slug options
     */
    'options' => [
        'source' => 'name',
        'destination' => 'slug',
        'unique' => true,
        'max_length' => 100,
        'separator' => '-',
        'language' => null,
    ],
];
