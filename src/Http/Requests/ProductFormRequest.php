<?php

namespace Ingenius\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class ProductFormRequest extends FormRequest
{
    /**
     * @return array
     */
    protected function imagesRules(): array
    {
        return [
            'new_images' => ['nullable', 'array'],
            'new_images.*' => ['mimes:jpg,jpeg,png,webp', 'max:500'],
            'removed_images' => ['nullable', 'array'],
            'removed_images.*' => ['integer'],
        ];
    }

    protected function categoriesRules(): array
    {
        return [
            'categories_ids' => ['required', 'array'],
            'categories_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }

    protected function comingSoonRules(): array
    {
        // Only add validation if the tenant has the coming soon feature
        if (!tenant() || !tenant()->hasFeature('coming-soon-product')) {
            return [];
        }

        return [
            'coming_soon' => ['nullable', 'boolean'],
            'available_from' => ['nullable', 'date', 'after:now'],
        ];
    }
}
