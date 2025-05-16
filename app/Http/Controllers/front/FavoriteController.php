<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\products;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Add a product to favorites
    public function addToFavorites(Request $request, $productId)
    {
        $user = $request->user();  // Get the authenticated user

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if product exists
        $product = products::find($productId);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Check if already favorited
        if ($user->favoriteProducts()->where('product_id', $productId)->exists()) {
            return response()->json(['message' => 'Product already in favorites'], 400);
        }

        // Add to favorites
        $user->favoriteProducts()->attach($productId);

        return response()->json(['message' => 'Product added to favorites'], 200);
    }


    /**
     * Remove a product from user’s favorites.
     */
    public function removeFromFavorites(Request $request, $productId)
    {
        $user = $request->user();  // Get the authenticated user
        // Check if already favorited
        if (!$user->favoriteProduct()->where('product_id', $productId)->exists()) {
            return response()->json(['message' => 'Product not in favorites'], 400);
        }
        // Remove from favorites
        $user->favoriteProduct()->detach($productId);
        return response()->json(['message' => 'Product removed from favorites'], 200);
    }

    /**
     * Check if a product is in user’s favorites.
     */
    public function isFavorite(Request $request, $productId)
    {
        $user = $request->user();  // Get the authenticated user
        // Check if already favorited
        if ($user->favoriteProduct()->where('product_id', $productId)->exists()) {
            return response()->json(['message' => 'Product is in favorites'], 200);
        }

    }
    /**
     * Get all favorite products of the user.
     */
    public function getFavorites(Request $request)
    {
        $user = $request->user();  // Get the authenticated user
        // Get all favorite products
        $favorites = $user->favoriteProducts()->get();
        return response()->json(['favorites' => $favorites], 200);
    }

}