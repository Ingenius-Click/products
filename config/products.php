<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify configuration options for the products package.
    |
    */

    'name' => 'Products',

    'sku_generator_implementation' => \Ingenius\Products\Services\SimpleSkuGenerator::class,

    /*
    |--------------------------------------------------------------------------
    | Settings Classes
    |--------------------------------------------------------------------------
    |
    | Here you can register settings classes for the products package.
    |
    */
    'settings_classes' => [
        \Ingenius\Products\Settings\ProductSettings::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Stock Availability Cache TTL
    |--------------------------------------------------------------------------
    |
    | The number of seconds to cache the computed available stock for a product.
    | Available stock accounts for reservations in carts and pending orders.
    | Lower values = more accurate but more DB queries.
    |
    */
    'stock_cache_ttl' => env('PRODUCTS_STOCK_CACHE_TTL', 300),
];
