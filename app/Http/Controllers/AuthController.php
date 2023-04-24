<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

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
            return response()->json($json_response);
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
            return response()->json($json_response);
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
}
