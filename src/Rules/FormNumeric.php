<?php

namespace Ingenius\Products\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FormNumeric implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // First convert string to numeric if possible
        if (is_string($value) && trim($value) !== '') {
            $value = is_numeric($value) ? (float) $value : $value;
        }

        // Then validate if it's numeric
        if (!is_numeric($value)) {
            $fail('The :attribute must be a number.');
        }
    }
}
