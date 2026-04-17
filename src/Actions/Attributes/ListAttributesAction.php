<?php

namespace Ingenius\Products\Actions\Attributes;

use Ingenius\Products\Models\Attribute;

class ListAttributesAction
{
    public function __invoke(array $filters = [])
    {
        $query = Attribute::with('options')->orderBy('position');

        return table_handler_paginate($filters, $query);
    }
}
