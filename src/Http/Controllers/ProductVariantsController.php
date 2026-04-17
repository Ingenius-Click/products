<?php

namespace Ingenius\Products\Http\Controllers;

use Ingenius\Core\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Ingenius\Auth\Helpers\AuthHelper;
use Ingenius\Products\Actions\Variants\BulkDeleteVariantsAction;
use Ingenius\Products\Actions\Variants\BulkUpdateVariantsAction;
use Ingenius\Products\Actions\Variants\CreateProductVariantAction;
use Ingenius\Products\Actions\Variants\DeleteProductVariantAction;
use Ingenius\Products\Actions\Variants\GenerateVariantsFromAttributesAction;
use Ingenius\Products\Actions\Variants\PreviewVariantsFromAttributesAction;
use Ingenius\Products\Actions\Variants\ListProductVariantsAction;
use Ingenius\Products\Actions\Variants\SetDefaultVariantAction;
use Ingenius\Products\Actions\Variants\UpdateProductVariantAction;
use Ingenius\Products\Actions\Variants\BulkCreateVariantsAction;
use Ingenius\Products\Http\Requests\BulkCreateVariantsRequest;
use Ingenius\Products\Http\Requests\BulkDeleteVariantsRequest;
use Ingenius\Products\Http\Requests\BulkUpdateVariantsRequest;
use Ingenius\Products\Http\Requests\GenerateVariantsRequest;
use Ingenius\Products\Http\Requests\ProductVariantRequest;
use Ingenius\Products\Http\Requests\UpdateProductVariantRequest;
use Ingenius\Products\Models\Product;
use Ingenius\Products\Models\ProductVariant;

class ProductVariantsController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Product $product, ListProductVariantsAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'viewAny', ProductVariant::class);

        return Response::api(
            'Product variants retrieved successfully',
            $action($product, $request->all())
        );
    }

    public function store(ProductVariantRequest $request, Product $product, CreateProductVariantAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'create', ProductVariant::class);

        $variant = $action($product, $request->validated());

        return Response::api(
            'Product variant created successfully',
            $variant,
            201
        );
    }

    public function show(Product $product, ProductVariant $variant): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'view', $variant);

        $variant->load('attributeOptions.attribute');

        return Response::api(
            'Product variant retrieved successfully',
            $variant
        );
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $variant, UpdateProductVariantAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'update', $variant);

        $variant = $action($variant, $request->validated());

        return Response::api(
            'Product variant updated successfully',
            $variant
        );
    }

    public function destroy(Product $product, ProductVariant $variant, DeleteProductVariantAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'delete', $variant);

        $action($variant);

        return Response::api(
            'Product variant deleted successfully'
        );
    }

    public function preview(GenerateVariantsRequest $request, Product $product, PreviewVariantsFromAttributesAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'viewAny', ProductVariant::class);

        $validated = $request->validated();
        $preview = $action($product, $validated['attribute_ids'], $validated['exclude_combinations'] ?? []);

        return Response::api(
            'Variant preview generated successfully',
            $preview
        );
    }

    public function generate(GenerateVariantsRequest $request, Product $product, GenerateVariantsFromAttributesAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'create', ProductVariant::class);

        $variants = $action($product, $request->validated());

        return Response::api(
            'Variants generated successfully',
            $variants,
            201
        );
    }

    public function setDefault(Product $product, ProductVariant $variant, SetDefaultVariantAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'update', $variant);

        $variant = $action($variant);

        return Response::api(
            'Default variant set successfully',
            $variant
        );
    }

    public function bulkStore(BulkCreateVariantsRequest $request, Product $product, BulkCreateVariantsAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'create', ProductVariant::class);

        $created = $action($product, $request->validated()['variants']);

        return Response::api(
            'Variants created successfully',
            $created,
            201
        );
    }

    public function bulkUpdate(BulkUpdateVariantsRequest $request, Product $product, BulkUpdateVariantsAction $action): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'update', new ProductVariant());

        $updated = $action($product, $request->validated()['variants']);

        return Response::api(
            'Variants updated successfully',
            $updated
        );
    }

    public function bulkDelete(BulkDeleteVariantsRequest $request, Product $product, BulkDeleteVariantsAction $action): JsonResponse {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'delete', new ProductVariant());

        $action($product, $request->validated()['variant_ids']);

        return Response::api(
            'Variants deleted successfully',
        );
    }
}
