<?php

namespace Ingenius\Products\Services;

use Ingenius\Products\Interfaces\ISkuGeneratorImplementation;

class SkuGenerator
{
    private ISkuGeneratorImplementation $implementation;

    public function __construct()
    {
        $implementationClass = config('products.sku_generator_implementation');
        if (!$implementationClass || !is_subclass_of($implementationClass, ISkuGeneratorImplementation::class)) {
            throw new \InvalidArgumentException("Invalid SKU generator implementation class.");
        }

        $this->implementation = new $implementationClass();
    }

    public function generateSku(): string
    {
        return $this->implementation->generateSku();
    }
}