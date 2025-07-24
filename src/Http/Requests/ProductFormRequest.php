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
            'images' => ['nullable', 'array'],
            'images.*' => ['mimes:jpg,jpeg,png,webp', 'max:300'],
            'removed_images' => ['nullable', 'array'],
            'removed_images.*' => ['integer'],
        ];
    }

    protected function categoriesRules(): array
    {
        return [
            'categories' => ['required', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
