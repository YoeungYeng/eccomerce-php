<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class SlideController extends Controller
{
    // index
    public function index()
    {
        try {
            $slide = Slide::all();
            return response()->json([
                'status' => 200,
                'message' => 'List of slides',
                'data' => $slide
            ],200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching slides',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // store 
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validation = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'image' => [
                    'required',
                    'image',
                    File::types(['jpeg', 'png', 'jpg', 'gif', 'webp'])
                        ->min(100) // Minimum file size in kilobytes (500KB)
                        ->max(12 * 1024), // Maximum file size in kilobytes (12MB)
                ],
            ]);
            // check if validation fails
            if ($validation->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation error',
                    'errors' => $validation->errors()
                ], 400);
            }
            // Store the image
            // Save image to database
            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('slide', 'public'); // Store in storage/app/public/images
                $image_url = asset('storage/' . $image_path); // Convert to URL
            } else {
                $image_url = null;
            }

            // Create a new slide
            $slide = Slide::create([
                'title' => $request->title,
                'subtitle' => $request->subTitle,
                'image' => $image_url,
            ]);
            // Return a success response
            return response()->json([
                'status' => true,
                'message' => 'Slide created successfully',
                'data' => $slide
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating slide',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // show 
    public function show($id)
    {
        try {
            $slide = Slide::find($id);
            // check if slide dont exist
            if ($slide == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Slide not found'
                ], 404);
            }
            // Return a success response
            return response()->json([
                'status' => true,
                'message' => 'Slide details',
                'data' => $slide
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching slide',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // update
    public function update(Request $request, $id, Slide $slide)
    {
        try {
            $product = Slide::find($id);

            if (!$product) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product not found',
                    'data' => [],
                ], 404);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'subtitle' => 'required|string|max:255',
                'image' => [
                    'required',
                    'image',
                    File::types(['jpeg', 'png', 'jpg', 'gif', 'webp'])
                        ->min(100) // Minimum file size in kilobytes (500KB)
                        ->max(12 * 1024), // Maximum file size in kilobytes (12MB)
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $product->title = $request->title;
            $product->subtitle = $request->subtitle;
            

            // dd($request->all()); // Inspect the request data
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::delete($product->image);
                }
                $image_path = $request->file('image')->store('slide', 'public');
                $image_url = asset('storage/' . $image_path); // Convert to URL
                $product->image = $image_url;

            }

            if ($product->isDirty()) {
                $product->save();
                return response()->json([
                    'message' => 'Product updated successfully 😊😊',
                    'product' => $product
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No changes detected ⚠️',
                    'product' => $product
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    // destroy
    public function destroy($id)
    {
        try {
            $slide = Slide::find($id);
            // check if slide dont exist
            if ($slide == null) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Slide not found'
                ], 404);
            }
            // delete the slide
            $slide->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Slide deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting slide',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
