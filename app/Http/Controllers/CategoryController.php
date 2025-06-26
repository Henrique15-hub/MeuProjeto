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
        return $this->categoryServices->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $request->isPersonalizada = true;

        return $this->categoryServices->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return $this->categoryServices->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        return $this->categoryServices->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->categoryServices->destroy($id);
    }
}
