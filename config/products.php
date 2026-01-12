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
];
