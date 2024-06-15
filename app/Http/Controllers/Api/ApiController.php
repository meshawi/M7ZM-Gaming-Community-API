<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Corrected namespace
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'validation_error',
                        'errors' => $validateUser->errors(),
                    ],
                    401
                );
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Encrypting the password
            ]);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'user_created',
                    'token' => $user->createToken("API TOKEN")->plainTextToken,
                ],
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                500
            );
        }
    }

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'validation_error',
                        'errors' => $validateUser->errors(),
                    ],
                    401
                );
            }
            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'invalid_credentials',
                    ],
                    401
                );
            }

            $user = User::where('email', $request->email)->first();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'user loged in seccessfully',
                    'token' => $user->createToken("API TOKEN")->plainTextToken,
                ],
                200
            );

        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $th->getMessage(),
                ],
                500
            );
        }
    }

    public function profile(Request $request)
    {
        $userData = auth()->user();
        return response()->json(
            [
                'status' => true,
                'message' => 'user_profile',
                'data' => $userData,
                'id' => $userData->id,
            ],
            200
        );
    }
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json(
            [
                'status' => true,
                'message' => 'user_logged_out',
                'data' => []
            ],
            200
        );
    }
}
