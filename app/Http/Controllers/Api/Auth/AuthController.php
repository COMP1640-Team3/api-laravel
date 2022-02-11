<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(UserLoginRequest $request): \Illuminate\Http\JsonResponse
    {
        // check email
        $user = User::where('email', $request->input('email'))->first();

        // check password
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json('Email or password is not correct!', 401);
        }
        $token = $user->createToken('myToken')->plainTextToken;
        $response = [
            'token' => $token,
            'user' => $user,
        ];
        return response()->json($response, 200);

    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json('Logout success', 200);
    }

    public function getRole(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = User::with('roles')->where('id', '=', $request->user()->id)->first();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 404);
        }
        return response()->json($user->roles[0], 200);
    }

    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = User::with('profile')->where('id', '=', $request->user()->id)->first();
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }

        return response()->json($user, 200);
    }
}
