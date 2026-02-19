<?php

namespace Ingenius\Products\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Ingenius\Products\Models\Category;

class ShowProductResource extends JsonResource {

    public function toArray(\Illuminate\Http\Request $request): array {

        return [
            ... $this->resource->toArray(),
            'categories' => $this->categories->map(fn(Category $category) => [
                'id' => $category->id,
                'name' => $category->name
            ])
        ];

    }

}