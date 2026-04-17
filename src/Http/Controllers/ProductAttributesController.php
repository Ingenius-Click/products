<?php

namespace Ingenius\Products\Http\Controllers;

use Ingenius\Core\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Ingenius\Auth\Helpers\AuthHelper;
use Ingenius\Products\Models\Product;

class ProductAttributesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get attributes assigned to a product.
     */
    public function index(Product $product): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'view', $product);

        $attributes = $product->attributes()->with('options')->orderBy('position')->get();

        return Response::api(
            'Product attributes retrieved successfully',
            $attributes
        );
    }

    /**
     * Sync attributes for a product.
     */
    public function sync(Product $product): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'update', $product);

        $data = request()->validate([
            'attribute_ids' => 'required|array',
            'attribute_ids.*' => 'integer|exists:attributes,id',
        ]);

        $product->attributes()->sync($data['attribute_ids']);

        return Response::api(
            'Product attributes synced successfully',
            $product->attributes()->with('options')->orderBy('position')->get()
        );
    }
}
