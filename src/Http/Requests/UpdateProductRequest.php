<?php

namespace Ingenius\Products\Http\Requests;

use Ingenius\Products\Events\ProductExtraRules;
use Ingenius\Products\Rules\FormNumeric;

class UpdateProductRequest extends ProductFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // If we can't get the product ID, fallback to null
        $productId = $this->product ?? null;

        if (is_object($productId) && method_exists($productId, 'getKey')) {
            $productId = $productId->getKey();
        }


        $rules = [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $productId,
            'description' => 'nullable|string',
            'visible' => 'required|boolean',
            'regular_price' => ['required', new FormNumeric, 'min:0'],
            'sale_price' => ['required', new FormNumeric, 'min:0'],
            'handle_stock' => 'required|boolean',
            'stock' => ['required', new FormNumeric, 'min:0'],
            'stock_for_sale' => ['required', new FormNumeric, 'min:0'],
            'unit_of_measurement' => 'nullable|string|max:255',
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
