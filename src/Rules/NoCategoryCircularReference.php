<?php

namespace Ingenius\Products\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Ingenius\Products\Models\Category;

class NoCategoryCircularReference implements ValidationRule
{
    /**
     * The category being validated
     */
    protected ?Category $category;

    /**
     * Create a new rule instance.
     */
    public function __construct(?Category $category = null)
    {
        $this->category = $category;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If value is null, it's a top-level category, which is always valid
        if ($value === null) {
            return;
        }

        // If there's no category being updated (i.e., this is a new category), the check is not needed
        if ($this->category === null) {
            return;
        }

        // Get the potential parent category
        $parentCategory = Category::find($value);
        if (!$parentCategory) {
            return; // If parent doesn't exist, let the exists:categories,id rule handle it
        }

        // Check if the parent category is the same as the current category
        if ($parentCategory->id === $this->category->id) {
            $fail('A category cannot be its own parent.');
            return;
        }

        // Check if the parent category is a descendant of the current category
        if ($parentCategory->isDescendantOf($this->category)) {
            $fail('Circular reference detected. A category cannot have one of its descendants as its parent.');
        }
    }
}
