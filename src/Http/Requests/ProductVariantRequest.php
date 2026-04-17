<?php

namespace Ingenius\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ingenius\Products\Rules\FormNumeric;

class ProductVariantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sku' => 'nullable|string|max:255|unique:product_variants,sku',
            'normal_regular_price' => ['nullable', new FormNumeric, 'min:0'],
            'normal_sale_price' => ['nullable', new FormNumeric, 'min:0', 'lte:normal_regular_price'],
            'handle_stock' => 'nullable|boolean',
            'stock' => ['required_if:handle_stock,true', 'nullable', new FormNumeric, 'min:0'],
            'stock_for_sale' => ['required_if:handle_stock,true', 'nullable', new FormNumeric, 'min:0', 'lte:stock'],
            'is_default' => 'nullable|boolean',
            'visible' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
            'attribute_option_ids' => 'nullable|array',
            'attribute_option_ids.*' => 'integer|exists:attribute_options,id',
            'new_images' => ['nullable', 'array'],
            'new_images.*' => ['mimes:jpg,jpeg,png,webp', 'max:500'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
