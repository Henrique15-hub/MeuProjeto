<?php

namespace App\Http\Controllers;

use App\Http\Requests\services\StoreCategoryRequest;
use App\Http\Requests\services\UpdateCategoryRequest;
use App\Services\CategoryServices;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    private $categoryServices;

    public function __construct()
    {
        $this->categoryServices = new CategoryServices;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryServices->index();

        return response()->json([
            'message' => 'showing all categories of the authenticated user',
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $category = $this->categoryServices->store($validatedData);

        return response()->json([
            'message' => 'category created succesfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryServices->show($id);

        if (! $category['success']) {
            return response()->json([
                'message' => 'category not found',
            ], 404);
        }

        return response()->json(array_merge([
            'message' => 'showing the category'],
            $category,
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();

        $category = $this->categoryServices->update($validatedData, $id);

        if (! $category['success']) {
            return response()->json([
                'message' => 'category not found',
            ], 404);
        }

        return response()->json(array_merge([
            'message' => 'category updated successfully'],
            $category
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryServices->destroy($id);

        if (! $category['success']) {
            return response()->json([
                'message' => 'category not found',
            ], 404);
        }

        return response()->json([
            'message' => 'category deleted successfully',
        ]);
    }
}
