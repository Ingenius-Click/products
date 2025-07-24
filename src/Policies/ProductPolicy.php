<?php

namespace Ingenius\Products\Policies;

use Ingenius\Auth\Models\User;
use Ingenius\Products\Constants\ProductsPermissions;
use Ingenius\Products\Models\Product;

class ProductPolicy
{
    public function viewAny(?User $user)
    {
        return true;
    }

    public function view(?User $user, Product $product)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->can(ProductsPermissions::PRODUCTS_CREATE);
    }

    public function update(User $user, Product $product)
    {
        return $user->can(ProductsPermissions::PRODUCTS_EDIT);
    }

    public function delete(User $user, Product $product)
    {
        return $user->can(ProductsPermissions::PRODUCTS_DELETE);
    }
}
