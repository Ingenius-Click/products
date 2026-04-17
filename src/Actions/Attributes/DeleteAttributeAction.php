<?php

namespace Ingenius\Products\Actions\Attributes;

use Illuminate\Support\Facades\DB;
use Ingenius\Products\Models\Attribute;

class DeleteAttributeAction
{
    public function __invoke(Attribute $attribute): void
    {
        DB::transaction(function () use ($attribute) {
            $attribute->delete();
        });
    }
}
