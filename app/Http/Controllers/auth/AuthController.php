<?php

namespace App\Http\Controllers\auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;

class AuthController extends Controller
{
    public function login (LoginUserRequest $request): JsonResponse {
        $validatedData = $request->validated();
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user or !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'message' => 'invalid credentials',
            ],422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'login completed with success',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout (): JsonResponse {
        $user = auth()->user();

        $user->tokens()->delete();

        return response()->json([
            'message' => 'logout completed with success',
        ]);
    }
}
