<?php

namespace Ingenius\Products\Policies;

use Ingenius\Products\Constants\AttributesPermissions;
use Ingenius\Products\Models\Attribute;

class AttributePolicy
{
    public function viewAny($user)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(AttributesPermissions::ATTRIBUTES_VIEW);
        }

        return false;
    }

    public function view($user, Attribute $attribute)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(AttributesPermissions::ATTRIBUTES_VIEW);
        }

        return false;
    }

    public function create($user)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(AttributesPermissions::ATTRIBUTES_CREATE);
        }

        return false;
    }

    public function update($user, Attribute $attribute)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(AttributesPermissions::ATTRIBUTES_EDIT);
        }

        return false;
    }

    public function delete($user, Attribute $attribute)
    {
        $userClass = tenant_user_class();

        if ($user && is_object($user) && is_a($user, $userClass)) {
            return $user->can(AttributesPermissions::ATTRIBUTES_DELETE);
        }

        return false;
    }
}
