<?php

namespace Ingenius\Products\Policies;

use Ingenius\Products\Constants\VariantsPermissions;
use Ingenius\Products\Models\ProductVariant;

class ProductVariantPolicy
{
    public function viewAny($user)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(VariantsPermissions::VARIANTS_VIEW);
        }

        return false;
    }

    public function view($user, ProductVariant $variant)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(VariantsPermissions::VARIANTS_VIEW);
        }

        return false;
    }

    public function create($user)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(VariantsPermissions::VARIANTS_CREATE);
        }

        return false;
    }

    public function update($user, ProductVariant $variant)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(VariantsPermissions::VARIANTS_EDIT);
        }

        return false;
    }

    public function delete($user, ProductVariant $variant)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(VariantsPermissions::VARIANTS_DELETE);
        }

        return false;
    }
}
