<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\products as ModelsProducts;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as Storage;

class products extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // all products
        return ModelsProducts::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $data = $request->validate([
                'category' => 'required|string|max:50',
                'product_name' => 'required|string|max:50', // ✅ Fixed: product-name -> product_name
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:1',
                'description' => 'required|string|max:250',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // Max 2MB
            ]);

            // Store image in the 'public/images' folder
            // $image_path = $request->file('image')->store('images', 'public'); // ✅ Fixed path

            // Handle file upload
            if ($request->hasFile('image')) {
                $image_path = $request->file('image')->store('images', 'public'); // Store in storage/app/public/images
                $image_url = asset('storage/' . $image_path); // Convert to URL
            } else {
                $image_url = null;
            }
            // Create product in the database
            $product = ModelsProducts::create([
                'category_name' => $data['category'],
                'product_name' => $data['product_name'], // ✅ Fixed key
                'price' => $data['price'],
                'quantity' => $data['quantity'],
                'description' => $data['description'],
                'image' => $image_url, // ✅ Correct image path
            ]);

            return response()->json($product, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error💔💔',
                'errors' => $e->getMessage() // ✅ Fixed: Use $e->errors() instead of getMessage()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!💔',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelsProducts $product)
    {
        //​ show display 
        return $product;

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsProducts $product)
    {
        // update product
        try {
            // ✅ Validate input
            $data = $request->validate([
                'category' => 'sometimes|string|max:50',
                'product_name' => 'sometimes|string|max:50',
                'price' => 'sometimes|numeric|min:0',
                'quantity' => 'sometimes|integer|min:1',
                'description' => 'sometimes|string|max:250',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048' // Optional image upload
            ]);

            // ✅ Assign only if values exist in the request
            if ($request->has('category')) {
                $product->category_name = $data['category'];
            }
            if ($request->has('product_name')) {
                $product->product_name = $data['product_name'];
            }
            if ($request->has('price')) {
                $product->price = $data['price'];
            }
            if ($request->has('quantity')) {
                $product->quantity = $data['quantity'];
            }
            if ($request->has('description')) {
                $product->description = $data['description'];
            }

            // ✅ Handle image upload
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::delete($product->image);
                }
                $product->image = $request->file('image')->store('images', 'public');
            }

            // ✅ Save only if there are changes
            if ($product->isDirty()) {
                $product->save();
                return response()->json([
                    'message' => 'Product updated successfully 🎉',
                    'product' => $product
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No changes detected ⚠️',
                    'product' => $product
                ], 200);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error ❌',
                'errors' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong! 💔',
                'error' => $e->getMessage()
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
