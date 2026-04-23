<?php

namespace Ingenius\Products\Http\Requests;

use DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ingenius\Products\Rules\FormNumeric;

class BulkUpdateVariantsRequest extends FormRequest
{
    // public function rules(): array
    // {
    //     return [
    //         'variants' => 'required|array|min:1',
    //         'variants.*.id' => 'required|integer|exists:product_variants,id',
    //         'variants.*.sku' => 'nullable|string|max:255|unique:product_variants,sku,variants.*.id',
    //         'variants.*.stock' => ['nullable', new FormNumeric, 'min:0'],
    //         'variants.*.normal_regular_price' => ['nullable', new FormNumeric, 'min:0'],
    //         'variants.*.normal_sale_price' => ['nullable', new FormNumeric, 'min:0'],
    //         'variants.*.visible' => 'nullable|boolean',
    //     ];
    // }

    /**
     * Rekey the `variants` array by each item's `id` so validation errors are
     * returned as `variants.{id}.field` instead of `variants.{index}.field`.
     * Items without a valid id keep their original index so the `required`
     * rule on `id` still fires with a meaningful key.
     */
    protected function prepareForValidation(): void
    {
        $variants = $this->input('variants');

        if (!is_array($variants)) {
            return;
        }

        $rekeyed = [];

        foreach ($variants as $index => $variant) {
            $key = is_array($variant) && isset($variant['id']) && is_numeric($variant['id'])
                ? (int) $variant['id']
                : $index;

            $rekeyed[$key] = $variant;
        }

        $this->merge(['variants' => $rekeyed]);
    }

    public function rules()
    {
        // 1. Define the base rules
        $rules = [
            'variants' => 'required|array|min:1',
        ];

        // 2. Fetch the variants from the request
        $variants = $this->input('variants', []);

        // 3. Loop through each variant to build dynamic rules
        foreach ($variants as $index => $variant) {
            $id = $variant['id'] ?? null;

            $rules["variants.$index.id"] = 'required|integer|exists:product_variants,id';
            $rules["variants.$index.is_default"] = 'nullable|boolean';
            
            // --- UNIQUE SKU LOGIC ---
            // distinct: ensures no duplicate SKUs inside the request itself
            // Rule::unique: checks DB but ignores the current variant's ID
            $rules["variants.$index.sku"] = [
                'nullable',
                'string',
                'max:255',
                'distinct', 
                Rule::unique('product_variants', 'sku')->ignore($id),
            ];

            $rules["variants.$index.stock"] = ['nullable', new FormNumeric, 'min:0'];
            $rules["variants.$index.normal_regular_price"] = ['nullable', new FormNumeric, 'min:0'];
            $rules["variants.$index.visible"] = 'nullable|boolean';

            // --- CONDITIONAL PRICE LOGIC ---
            $rules["variants.$index.normal_sale_price"] = [
                'nullable',
                new FormNumeric,
                'min:0',
                function ($attribute, $value, $fail) use ($variant, $id) {
                    if (is_null($value)) return;

                    $regularPrice = null;

                    // Scenario A: Regular price is provided in the request
                    if (isset($variant['normal_regular_price']) && $variant['normal_regular_price'] !== '') {
                        $regularPrice = (float) $variant['normal_regular_price'];
                    } 
                    // Scenario B: Not in request, fetch actual regular price from the DB
                    elseif ($id) {
                        // Update 'regular_price' to match your actual database column name
                        $dbVariant = DB::table('product_variants')->where('id', $id)->first();
                        if ($dbVariant) {
                            $regularPrice = (float) $dbVariant->regular_price / 100; 
                        }
                    }

                    // If we found a regular price, compare them
                    if ($regularPrice !== null && (float) $value > $regularPrice) {
                        $fail(__('validation.lte.numeric', ['attribute' => 'precio de venta', 'value' => $regularPrice]));
                    }
                }
            ];
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'variants.*.id' => 'ID',
            'variants.*.sku' => 'SKU',
            'variants.*.stock' => 'stock',
            'variants.*.normal_regular_price' => 'precio regular',
            'variants.*.normal_sale_price' => 'precio de venta',
            'variants.*.visible' => 'visible',
        ];
    }
}
