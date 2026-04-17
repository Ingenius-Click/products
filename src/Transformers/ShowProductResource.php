<?php

namespace Ingenius\Products\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Ingenius\Products\Models\Category;

class ShowProductResource extends JsonResource {

    public function toArray(\Illuminate\Http\Request $request): array {

        $data = [
            ... $this->resource->toArray(),
            'categories' => $this->categories->map(fn(Category $category) => [
                'id' => $category->id,
                'name' => $category->name
            ]),
        ];

        // Include variants data if the product has variants
        if ($this->resource->relationLoaded('variants') || $this->resource->hasVariants()) {
            $data['variants'] = $this->resource->variants()
                ->with('attributeOptions.attribute')
                ->orderBy('position')
                ->get();
            $data['attributes'] = $this->resource->attributes()
                ->with('options')
                ->orderBy('position')
                ->get();
            $data['has_variants'] = true;
        } else {
            $data['has_variants'] = false;
        }

        return $data;

    }

}