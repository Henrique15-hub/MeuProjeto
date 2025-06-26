<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryServices
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = auth()->user()->category()->get();

        return response()->json([
            'message' => 'showing all categories of the authenticated user',
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request): JsonResponse
    {
        $validatedData = $request->validated();

        $category = Category::create([
            'name' => $validatedData['name'],
            'isPersonalizada' => true,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'category created with success',
            'category' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $category = auth()->user()->category()->find($id);

        if (! $category) {
            return response()->json([
                'message' => 'category not found',
            ], 404);
        }

        return response()->json([
            'message' => 'showing the category',
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, int $id): JsonResponse
    {
        $validatedData = $request->validated();

        $category = auth()->user()->category()->find($id);

        if (! $category) {
            return response()->json([
                'message' => 'category not found',
            ], 404);
        }

        $category->update($validatedData);

        return response()->json([
            'message' => 'category updated with success',
            'category' => $category->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $category = auth()->user()->category()->find($id);

        if (! $category) {
            return response()->json([
                'message' => 'category not found',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'category deleted with success',
        ]);
    }
}
