<?php

namespace App\Services;

use App\Enum\DefaultCategoriesEnum;
use App\Models\Category;
use App\Models\User;

class CategoryServices
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::whereIn('user_id', [null, $this->user->id])
            ->orWhereNull('user_id')
            ->get();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($validatedData)
    {
        return Category::create([
            'name' => $validatedData['name'],
            'isPersonalizada' => true,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $category = Category::whereIn('user_id', [null, $this->user->id])
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

        $category = Category::whereIn('user_id', [null, $this->user->id])
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
        $category = $this->user->category()->find($id);

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

    public static function findFirstOrCreateCategory($validatedData, $user)
    {
        if (! empty($validatedData['category_name'])) {

            return Category::firstOrCreate([
                'name' => mb_convert_case($validatedData['category_name'], 2),
                'isPersonalizada' => true,
                'user_id' => $user->id,
            ]);
        }

        $description = str()->ascii(mb_strtoupper($validatedData['description']));

        foreach (DefaultCategoriesEnum::cases() as $categoryEnum) {
            foreach ($categoryEnum->keywords() as $keyword) {
                if (str_contains($description, $keyword)) {
                    return Category::where('name', mb_convert_case($categoryEnum->value, 2))->first();
                }

            }
        }

        return Category::firstOrCreate([
            'name' => 'Outros',
            'isPersonalizada' => false,
        ]);
    }
}
