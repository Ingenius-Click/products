<?php

namespace Ingenius\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteVariantsRequest extends FormRequest {

    public function rules(): array {

        return [
            'variant_ids' => ['required', 'array'],
            'variant_ids.*' => ['exists:product_variants,id']
        ];

    }

}