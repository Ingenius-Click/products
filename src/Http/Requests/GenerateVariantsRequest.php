<?php

namespace Ingenius\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ingenius\Products\Rules\FormNumeric;

class GenerateVariantsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'attribute_ids' => 'required|array|min:1',
            'attribute_ids.*' => 'integer|exists:attributes,id',
            'exclude_combinations' => 'nullable|array',
            'exclude_combinations.*' => 'array',
            'exclude_combinations.*.*' => 'integer|exists:attribute_options,id',
            'replace_existing' => 'nullable|boolean',
            'handle_stock' => 'nullable|boolean',
            'stock' => ['nullable', new FormNumeric, 'min:0'],
            'visible' => 'nullable|boolean',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
