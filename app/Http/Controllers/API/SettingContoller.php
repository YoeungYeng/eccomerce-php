<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingContoller extends Controller
{
    // index
    public function index()
    {
        try {
            $setting = Setting::orderBy('created_at', 'desc')->first();
            return response()->json([
                'status' => 200,
                'data' => $setting
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    // store
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'link' => 'required|url',
                'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // optional logo field
            ]);
            // If validation fails, return error response
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => $validator->errors()
                ], 400);
            }
            // Handle file upload for logo if present
            if ($request->hasFile('logo')) {
                // check if image is valid
                $image = $request->file('logo');
                if (!$image->isValid()) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Invalid image file',
                    ], 400);
                }
            }

            // Save image to database
            if ($request->hasFile('logo')) {
                $image_path = $request->file('logo')->store('logos', 'public'); // Store in storage/app/public/images
                $logoPath = asset('storage/' . $image_path); // Convert to URL
            }else {
                $logoPath = null; // If no logo is uploaded, set to null
            }
            $setting = Setting::create(
                [
                    'title' => $request->title,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'link' => $request->link,
                    'logo' => $logoPath
                ]
            );
            return response()->json([
                'status' => 200,
                'data' => $setting
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
