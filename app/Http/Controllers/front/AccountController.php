<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\orders;
use App\Models\Slide;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AccountController extends Controller
{
    // register new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);
        // check validation
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()
            ], 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'customer';
        $user->save();

        return response()->json([
            'status' => 201,
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);
    }

    // Login a user
    public function login(Request $request)
    {
        try {
            // Validate the request
            $data = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if ($data->fails()) {
                return response()->json([
                    'status' => 400,
                    'error' => $data->errors()
                ], 400);
            }
            // Try to generate token
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Get authenticated user
            $user = JWTAuth::user();

            // Check if user is admin
            if ($user->role !== 'customer') {
                return response()->json([
                    'status' => 403,
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }
            // Find the user by email
            if (JWTAuth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = User::find(Auth::user()->id);
                // check if admin
                if ($user->role == 'customer') {
                    // Generate a new token
                    $token = JWTAuth::fromUser($user);
                    // Return the token and user information
                    

                    return response()->json([
                        'status' => 200,
                        'token' => $token,
                        'id' => $user->id,
                        'email' => $user->email,
                        'password' => $user->password,
                        // 'user' => $user,

                    ], 200);
                } else {
                    return response()->json([
                        'status' => 401,
                        'message' => "You are not authorized to access admin panel"
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "Either email/password in incorrenct"
                ], 401);
            }
        } catch (ValidationException $e) {
            // ✅ Fixed syntax error in status code
            return response()->json(['error' => $e->getMessage()], 422);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }

    // get order details
    public function getOrderDetails($id, Request $request)
    {
        $order = orders::where([
            'user_id' => $request->user()->id,
            'id' => $id
        ])->with('item')->first();

        // check if order don't exist
        if ($order == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found',
                'data' => []
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Order found',
                'data' => $order
            ], 200);
        }

    }

    // get order 
    public function getOrders(Request $request)
    {
        $order = orders::where('user_id', $request->user()->id)->with('item')->get();
        if ($order) {
            return response()->json([
                "status" => 200,
                "message" => "You have succefully place order ",
                "data" => $order,
            ], 200);
        } else {
            return response()->json([
                "status" => 400,
                "message" => "Your cart is empty",
                "data" => null,
            ], 400);
        }
    }
    // get all slides
    public function getSlides()
    {
        $slides = Slide::all();
        if ($slides) {
            return response()->json([
                "status" => 200,
                "message" => "All slides",
                "data" => $slides,
            ], 200);
        } else {
            return response()->json([
                "status" => 400,
                "message" => "No slides found",
                "data" => null,
            ], 400);
        }
    }
    // Logout a user
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user || !$user->currentAccessToken()) {
                return response()->json(['error' => 'No active session or token found.'], 401);
            }

            // Revoke the current access token
            $user->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong during logout!',
                'details' => $e->getMessage() // Optional: remove in production
            ], 500);
        }
    }
    // Get authenticated user
    public function user(Request $request)
    {
        try {
            $user = User::find($request->user()->id);
            if (!$user) {
                return response()->json(['error' => 'User not found!'], 404);
            }
            // Check if the authenticated user is the same as the user being updated
            if ($request->user()->id !== $user->id) {
                return response()->json(['error' => 'Unauthorized action!'], 403);
            }
            // Return the authenticated user

            return response()->json([
                'status' => 200,
                'message' => 'User found',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }
    // Update user profile
    public function updateProfile(Request $request)
    {
        try {
            $user = User::find($request->user()->id); // Find user by ID or fail
            if (!$user) {
                return response()->json(['error' => 'User not found!'], 404);
            }
            // Check if the authenticated user is the same as the user being updated
            if ($request->user()->id !== $user->id) {
                return response()->json(['error' => 'Unauthorized action!'], 403);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                // 'email' => 'required|email|unique:users,email,' . '$request->user()->id'. 'id',
                'mobile' => 'required|string|max:15',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()
                ], 400);
            }

            // Update user fields
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->address = $request->address;
            $user->city = $request->city;
            $user->state = $request->state;
            $user->country = $request->country;
            $user->zip = $request->zip;

            // Save the user

            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'Profile updated successfully!',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'Something went wrong!',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    // Delete user account
    public function deleteAccount(Request $request)
    {
        try {
            // Delete the user
            $user = User::find($request->user()->id);
            $user->delete();

            return response()->json(['message' => 'Account deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }
    // Reset password
    public function resetPassword(Request $request)
    {
        try {
            // Validate the request
            $data = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ]);

            if ($data->fails()) {
                return response()->json([
                    'status' => 400,
                    'error' => $data->errors()
                ], 400);
            }

            // Find the user by email
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found!'], 404);
            }

            // Update the password
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['message' => 'Password reset successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }
    // Change password
    public function changePassword(Request $request)
    {
        try {
            // Validate the request
            $data = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6'
            ]);

            if ($data->fails()) {
                return response()->json([
                    'status' => 400,
                    'error' => $data->errors()
                ], 400);
            }

            // Check if the current password is correct
            if (!Hash::check($request->current_password, $request->user()->password)) {
                return response()->json(['error' => 'Current password is incorrect!'], 401);
            }

            // Update the password
            $user = User::find($request->user()->id);
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['message' => 'Password changed successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong! 💔'], 500);
        }
    }
}
