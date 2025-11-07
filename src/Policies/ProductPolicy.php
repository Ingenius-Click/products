<?php

namespace Ingenius\Products\Policies;

use Ingenius\Products\Constants\ProductsPermissions;
use Ingenius\Products\Models\Product;

class ProductPolicy
{
    public function viewAny($user)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ProductsPermissions::PRODUCTS_VIEW);
        }

        return false;
    }

    public function view($user, Product $product)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ProductsPermissions::PRODUCTS_VIEW);
        }

        return false;
    }

    public function create($user)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ProductsPermissions::PRODUCTS_CREATE);
        }

        return false;
    }

    public function update($user, Product $product)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ProductsPermissions::PRODUCTS_EDIT);
        }

        return false;
    }

    public function delete($user, Product $product)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(ProductsPermissions::PRODUCTS_DELETE);
        }

        return false;
    }
}
