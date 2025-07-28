<?php

namespace Ingenius\Products\Policies;

use Ingenius\Products\Constants\CategoriesPermissions;
use Ingenius\Products\Models\Category;

class CategoryPolicy
{

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Category $category): bool
    {
        return true;
    }

    public function create($user): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(CategoriesPermissions::CATEGORIES_CREATE);
        }

        return false;
    }

    public function update($user, Category $category): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(CategoriesPermissions::CATEGORIES_EDIT);
        }

        return false;
    }

    public function delete($user, Category $category): bool
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(CategoriesPermissions::CATEGORIES_DELETE);
        }

        return false;
    }
}
