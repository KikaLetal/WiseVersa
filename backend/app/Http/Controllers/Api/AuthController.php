<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    private function userData($user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'profile_picture' => $user->profile_picture,
            'role' => $user->role,
        ];
    }

    public function ping()
    {
        return response()->json([
            'success' => true,
            'ok' => true,
        ]);
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'unique:users,username',
            ],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
            'avatar' => [
                'nullable',
                'image',
                'max:5120'
            ],
        ]);

        $user = $this->userService->createUser(
            [
                'username' => $validatedData['username'],
                'password' => $validatedData['password'],
            ],
            $request->file('avatar')
        );

        $token = $user->createToken('wiseversa-web')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->userData($user),
        ], 201);
    }

    public function login(Request $request){
        $validatedData = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = $this->userService->loginUser($validatedData);

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => ['Неверный логин или пароль.'],
            ]);
        }

        $token = $user->createToken('wiseversa-web')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->userData($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $this->userData($request->user()),
        ]);
    }
}
