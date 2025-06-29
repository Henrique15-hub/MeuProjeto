<?php

namespace App\Services;

use App\Models\Category;

class CategoryServices
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::whereIn('user_id', [0, auth()->id()])->get();

        return $categories;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($validatedData)
    {
        $category = Category::create([
            'name' => $validatedData['name'],
            'isPersonalizada' => true,
            'user_id' => auth()->id(),
        ]);

        return $category;
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $category = Category::whereIn('user_id', [0, auth()->id()])
            ->where('id', $id)->first();

        if (! $category) {
            return [
                'success' => false,
            ];
        }

        return [
            'success' => true,
            'category' => $category,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($validatedData, int $id)
    {

        $category = Category::whereIn('user_id', [0, auth()->id()])
            ->where('id', $id)->first();

        if (! $category) {
            return [
                'success' => false,
            ];
        }

        $category->update($validatedData);

        return [
            'success' => true,
            'category' => $category->fresh(),
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $category = auth()->user()->category()->find($id);

        if (! $category) {
            return [
                'success' => false,
            ];
        }

        $category->delete();

        return [
            'success' => true,
        ];
    }
}
