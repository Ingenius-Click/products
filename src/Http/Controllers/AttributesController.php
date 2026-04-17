<?php

namespace Ingenius\Products\Http\Controllers;

use Ingenius\Core\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Ingenius\Auth\Helpers\AuthHelper;
use Ingenius\Products\Actions\Attributes\CreateAttributeAction;
use Ingenius\Products\Actions\Attributes\DeleteAttributeAction;
use Ingenius\Products\Actions\Attributes\ListAttributesAction;
use Ingenius\Products\Actions\Attributes\ShowAttributeAction;
use Ingenius\Products\Actions\Attributes\UpdateAttributeAction;
use Ingenius\Products\Http\Requests\AttributeRequest;
use Ingenius\Products\Http\Requests\UpdateAttributeRequest;
use Ingenius\Products\Models\Attribute;

class AttributesController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, ListAttributesAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'viewAny', Attribute::class);

        return Response::api(
            'Attributes retrieved successfully',
            $action($request->all())
        );
    }

    public function store(AttributeRequest $request, CreateAttributeAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'create', Attribute::class);

        $attribute = $action($request->validated());

        return Response::api(
            'Attribute created successfully',
            $attribute,
            201
        );
    }

    public function show(Attribute $attribute, ShowAttributeAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'view', $attribute);

        return Response::api(
            'Attribute retrieved successfully',
            $action($attribute)
        );
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute, UpdateAttributeAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'update', $attribute);

        $attribute = $action($attribute, $request->validated());

        return Response::api(
            'Attribute updated successfully',
            $attribute
        );
    }

    public function destroy(Attribute $attribute, DeleteAttributeAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'delete', $attribute);

        $action($attribute);

        return Response::api(
            'Attribute deleted successfully'
        );
    }
}
