<?php

namespace Ingenius\Products\Http\Controllers;

use Ingenius\Core\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ingenius\Auth\Helpers\AuthHelper;
use Ingenius\Products\Actions\CreateProductAction;
use Ingenius\Products\Actions\DeleteProductAction;
use Ingenius\Products\Actions\ListProductsAction;
use Ingenius\Products\Actions\ShowProductAction;
use Ingenius\Products\Actions\UpdateProductAction;
use Ingenius\Products\Http\Requests\ProductRequest;
use Ingenius\Products\Http\Requests\UpdateProductRequest;
use Ingenius\Products\Models\Product;

class ProductsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ListProductsAction $listProductsAction): JsonResponse
    {
        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'viewAny', Product::class);

        $products = $listProductsAction($request->all());

        return response()->api(
            'Products retrieved successfully',
            $products
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request, CreateProductAction $createProductAction): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'create', Product::class);

        $product = $createProductAction($request->validated());

        return response()->api(
            'Product created successfully',
            $product,
            201
        );
    }

    /**
     * Show the specified resource.
     */
    public function show(Product $product, ShowProductAction $showProductAction): JsonResponse
    {
        $product = $showProductAction($product);

        return response()->api(
            'Product retrieved successfully',
            $product
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product, UpdateProductAction $updateProductAction): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'update', $product);

        $product = $updateProductAction($product, $request->validated());

        return response()->api(
            'Product updated successfully',
            $product
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, DeleteProductAction $deleteProductAction): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'delete', $product);

        $deleteProductAction($product);

        return response()->api(
            'Product deleted successfully'
        );
    }
}
