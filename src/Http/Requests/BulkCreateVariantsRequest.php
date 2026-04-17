<?php

namespace Ingenius\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ingenius\Products\Rules\FormNumeric;

class BulkCreateVariantsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'nullable|string|max:255|unique:product_variants,sku',
            'variants.*.normal_regular_price' => ['nullable', new FormNumeric, 'min:0'],
            'variants.*.normal_sale_price' => ['nullable', new FormNumeric, 'min:0'],
            'variants.*.handle_stock' => 'nullable|boolean',
            'variants.*.stock' => ['required_if:variants.*.handle_stock,true', 'nullable', new FormNumeric, 'min:0'],
            'variants.*.stock_for_sale' => ['required_if:variants.*.handle_stock,true', 'nullable', new FormNumeric, 'min:0'],
            'variants.*.is_default' => 'nullable|boolean',
            'variants.*.visible' => 'nullable|boolean',
            'variants.*.position' => 'nullable|integer|min:0',
            'variants.*.attribute_option_ids' => 'nullable|array',
            'variants.*.attribute_option_ids.*' => 'integer|exists:attribute_options,id',
            'variants.*.new_images' => ['nullable', 'array'],
            'variants.*.new_images.*' => ['mimes:jpg,jpeg,png,webp', 'max:500'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
