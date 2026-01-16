<?php

namespace Ingenius\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Ingenius\Products\Rules\NoCategoryCircularReference;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id', new NoCategoryCircularReference()],
            'new_images' => ['nullable', 'array'],
            'new_images.*' => ['mimes:jpg,jpeg,png,webp','max:300',"dimensions:ratio=1/1"],
            'removed_images' => ['nullable', 'array'],
            'removed_images.*' => ['integer'],
        ];
    }
}
