<?php

namespace Ingenius\Products\Policies;

use Ingenius\Auth\Models\User;
use Ingenius\Products\Constants\CategoriesPermissions;
use Ingenius\Products\Models\Category;

class CategoryPolicy
{

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(CategoriesPermissions::CATEGORIES_CREATE);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can(CategoriesPermissions::CATEGORIES_EDIT);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can(CategoriesPermissions::CATEGORIES_DELETE);
    }
}
