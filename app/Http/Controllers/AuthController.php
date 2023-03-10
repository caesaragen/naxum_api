<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'phone' => 'required|string|unique:users,phone',
                'password' => 'required|string'
            ]);
        } catch (\Exception $e) {
            return response(['message' => 'Validation error occurred.'], 422);
        }
        
        $firstName = $fields['first_name'];
        $lastName = $fields['last_name'];
        $username = strtolower(substr($firstName, 0, 1) . $lastName);

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'email' => $fields['email'],
            'phone' => $fields['phone'],
            'password' => bcrypt($fields['password'])
        ]);
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check username
        $user = User::where('username', $fields['username'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
