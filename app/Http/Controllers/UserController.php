<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\user\StoreUserRequest;
use App\Http\Requests\user\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::all();

        return response()->json([
            'message' => 'all the users of the database',
            'users' => $users,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = User::create($validatedData);

        $wallet = Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        return response()->json([
            'message' => 'user created with success',
            'user' => $user->name,
            'wallet' => $wallet,
        ],201);

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = auth()->user();

        $user->update($validatedData);

        return response()->json([
            'message' => 'user updated with success',
            'user' => $user->fresh(),
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): JsonResponse
    {
        $user = auth()->user();
        $user->tokens()->delete();

        $user->wallet()->delete();
        $user->delete();

        return response()->json([
            'message' => 'user deleted with success',
        ]);
    }
}
