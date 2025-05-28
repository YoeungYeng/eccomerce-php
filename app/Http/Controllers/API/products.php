<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\products as ModelsProducts;
use App\Models\TempImage;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;


class products extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // all products

        $product = ModelsProducts::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 200, // success
            'message' => 'All Products',
            'data' => $product // return all brands
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => "required",
                // 'price' => "required|numeric",
                'category' => 'required|integer',
                'brands' => 'required|integer',
                'status' => 'required',
                'is_feature' => 'required',
                // 'image' => '|image|mimes:png,jpg,jpeg,gif|max:2048' // 5MB max image size
            ]);

            // if validation fails
            if ($validator->fails()) {
                return response()->json([
                    "status" => 400,
                    'message' => $validator->errors()
                ], 400);
            }

            // store product
            $product = ModelsProducts::create([
                'title' => $request->title,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'category_id' => $request->category,
                'brand_id' => $request->brands,
                'status' => $request->status,
                'is_feature' => $request->is_feature
            ]);
            // check if product created
            if (!$product) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Product not created',
                ], 500);
            }
            // check if image is uploaded
            if ($request->hasFile('image')) {
                // check if image is valid
                $image = $request->file('image');
                if (!$image->isValid()) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Invalid image file',
                    ], 400);
                }
            }

            // Save image to database
            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('product/larg', 'public'); // Store in storage/app/public/images
                $image_url = asset('storage/' . $image_path); // Convert to URL
            } else {
                $image_url = null;
            }

            $product->image = $image_url; // Save generated image URL
            $product->save();

            // response from server after request
            return response()->json([
                'status' => 201,
                'message' => 'Product has been created ❤️',
                'data' => $product
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong! 💔🤣',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //​ show display 
        $product = ModelsProducts::find($id);
        // check if product exists
        if ($product == null) {
            return response()->json([
                'status' => 404, // not found
                'message' => 'Brand Not Found',
            ], 404);
        }
        return response()->json([
            'status' => 200, // success
            'message' => 'Brand Found',
            'data' => $product // return product
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $product = ModelsProducts::find($id);

            if (!$product) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product not found',
                    'data' => [],
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string', // Title is now always required
                'price' => 'required|numeric',
                'category' => 'required|integer',
                'brands' => 'required|integer|exists:brand,id',
                'status' => 'required',
                'is_feature' => 'required',
                'image' => 'image|mimes:png,jpg,jpeg,gif|max:2048',
                // ... other fields
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Update product fields
            $product->title = $request->input('title', $product->title);
            $product->price = $request->input('price', $product->price);
            $product->quantity = $request->input('quantity', $product->quantity);
            $product->description = $request->input('description', $product->description);
            $product->short_description = $request->input('short_description', $product->short_description);
            $product->category_id = $request->input('category', $product->category_id);
            $product->brand_id = $request->input('brands', $product->brand_id);
            $product->status = $request->input('status', $product->status);
            $product->is_feature = $request->input('is_feature', $product->is_feature);

            // dd($request->all()); // Inspect the request data
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::delete($product->image);
                }
                $image_path = $request->file('image')->store('product/larg', 'public');
                $image_url = asset('storage/' . $image_path); // Convert to URL
                $product->image = $image_url;

            }

            if ($product->isDirty()) {
                $product->save();
                return response()->json([
                    'message' => 'Product updated successfully',
                    'product' => $product
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No changes detected',
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




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find product by ID
            $product = ModelsProducts::findOrFail($id);

            // Delete the associated image if it exists
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }

            // Delete product from database
            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully 🚀❤️'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found 💔💔',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong! 💔',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
