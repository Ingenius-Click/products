<?php

namespace Ingenius\Products\Actions\Variants;

use Illuminate\Support\Facades\DB;
use Ingenius\Products\Models\Product;

class BulkCreateVariantsAction
{
    public function __invoke(Product $product, array $variantsData): array
    {
        return DB::transaction(function () use ($product, $variantsData) {
            $createAction = app(CreateProductVariantAction::class);
            $created = [];

            foreach ($variantsData as $data) {
                $created[] = $createAction($product, $data);
            }

            return $created;
        });
    }
}
