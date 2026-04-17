<?php

namespace Ingenius\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|in:text,color,image',
            'position' => 'nullable|integer|min:0',
            'options' => 'nullable|array',
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
