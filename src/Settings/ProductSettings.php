<?php

namespace Ingenius\Products\Settings;

use Ingenius\Core\Settings\Settings;

class ProductSettings extends Settings 
{
    public bool $auto_sku_generation = true;

    public string $sku_prefix = 'SKU-';

    public static function group(): string
    {
        return 'products';
    }

}