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
];
