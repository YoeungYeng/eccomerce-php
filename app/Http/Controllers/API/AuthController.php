<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {

        try {
            // Validate the request
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6'
            ]);

            // Create a new user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return response
            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 201);

        } catch (ValidationException $e) {
            // FIXED: Corrected the syntax for returning validation errors
            return response()->json(['error' => $e->errors()], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }

    // Login a user
    public function login(Request $request)
    {
        try {
            // Validate the request
            $data = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            // Find the user by email
            $user = User::where('email', $data['email'])->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($data['password'], $user->password)) {
                return response()->json(['error' => 'Invalid credentials!'], 401);
            }

            // Create a new token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return successful response
            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);

        } catch (ValidationException $e) {
            // ✅ Fixed syntax error in status code
            return response()->json(['error' => $e->errors()], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }

}
