<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login (Request $request) {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $ve) {
            $json_response = [
                'success' => false,
                'errors' => $ve->errors(),
                'data' => [],
            ];
            return response()->json($json_response, 422);
        }
        $user_auth = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);
    
        if (!$user_auth) {
            $json_response = [
                'success' => false,
                'errors' => [
                    'login' => ['these credentials don\'t match our records'],
                ],
                'data' => []
            ];
            return response()->json($json_response, 401);
        }
    
        $user = Auth::user();
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        $json_response = [
            'success' => true,
            'errors' => [],
            'data' => [
                'access_token' => $token
            ]
        ];
    
        return response()->json($json_response);
    }

    public function register(Request $request) {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'errors' => $ve->errors(),
                'data' => [],
            ], 422);
        }
        
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);    
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'message' => $th->getMessage(),
                ],
                'data' => [],
            ], 500);
        }
        return response()->json([
            'success' => true,
            'errors' => [],
            'data' => [
                'user' => $user,
            ],
        ]);
    }
}
