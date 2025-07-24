<?php

namespace Ingenius\Products\Http\Controllers;

use Ingenius\Core\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Ingenius\Auth\Helpers\AuthHelper;
use Ingenius\Products\Actions\CreateCategoryAction;
use Ingenius\Products\Actions\DeleteCategoryAction;
use Ingenius\Products\Actions\ListCategoriesAction;
use Ingenius\Products\Actions\ShowCategoryAction;
use Ingenius\Products\Actions\UpdateCategoryAction;
use Ingenius\Products\Http\Requests\CategoryRequest;
use Ingenius\Products\Http\Requests\UpdateCategoryRequest;
use Ingenius\Products\Models\Category;
use Ingenius\Products\Transformers\CategoryListResource;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ListCategoriesAction $listCategoriesAction): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'viewAny', Category::class);

        $filters = $request->only(['parent_id', 'search', 'per_page']);
        $categories = $listCategoriesAction($filters);

        return Response::api(
            'Categories retrieved successfully',
            $categories->through(fn(Category $category) => new CategoryListResource($category))
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request, CreateCategoryAction $createCategoryAction): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'create', Category::class);

        $category = $createCategoryAction($request->validated());

        return Response::api(
            'Category created successfully',
            $category,
            201
        );
    }

    /**
     * Show the specified resource.
     */
    public function show(Category $category, ShowCategoryAction $showCategoryAction): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'view', $category);

        $category = $showCategoryAction($category);

        return Response::api(
            'Category retrieved successfully',
            $category
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $updateCategoryAction): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'update', $category);

        $category = $updateCategoryAction($category, $request->validated());

        return Response::api(
            'Category updated successfully',
            $category
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, DeleteCategoryAction $deleteCategoryAction): JsonResponse
    {
        $user = AuthHelper::getUser();
        $this->authorizeForUser($user, 'delete', $category);

        $deleteCategoryAction($category);

        return Response::api(
            'Category deleted successfully'
        );
    }
}
