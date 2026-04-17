<?php

namespace Ingenius\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'type' => 'nullable|string|in:text,color,image',
            'position' => 'nullable|integer|min:0',
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|integer|exists:attribute_options,id',
            'options.*.name' => 'required_with:options|string|max:255',
            'options.*.value' => 'nullable|string|max:255',
            'options.*.position' => 'nullable|integer|min:0',
            'options.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:500'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
