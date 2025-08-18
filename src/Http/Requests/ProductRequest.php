<?php

namespace Ingenius\Products\Http\Requests;

use Ingenius\Products\Events\ProductExtraRules;
use Ingenius\Products\Rules\FormNumeric;

class ProductRequest extends ProductFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'visible' => 'nullable|boolean',
            'normal_regular_price' => ['required', new FormNumeric, 'min:0'],
            'normal_sale_price' => ['required', new FormNumeric, 'min:0', 'lte:normal_regular_price'],
            'handle_stock' => 'nullable|boolean',
            'stock' => ['required_if:handle_stock,true', new FormNumeric, 'min:0'],
            'stock_for_sale' => ['required_if:handle_stock,true', new FormNumeric, 'min:0', 'lte:stock'],
            'unit_of_measurement' => 'required|string|max:255',
            'short_description' => 'required|string|max:255',
            ...$this->imagesRules(),
            ...$this->categoriesRules(),
        ];

        $event = new ProductExtraRules($rules);
        event($event);

        return $event->getRules();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
