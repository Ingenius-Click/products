<?php

namespace Ingenius\Products\Actions\Attributes;

use Ingenius\Products\Models\Attribute;

class ShowAttributeAction
{
    public function __invoke(Attribute $attribute): Attribute
    {
        return $attribute->load('options');
    }
}
