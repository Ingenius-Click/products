<?php

namespace Ingenius\Products\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'images' => $this->images,
            'parent_id' => $this->parent_id ? [
                'id' => $this->parent_id,
                'name' => $this->parent?->name
            ] : null,
        ];
    }
}
