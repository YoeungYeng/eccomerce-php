<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FooterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 
        try {
            $footer = Footer::orderBy('created_at', 'desc')->get();
            return response()->json([
                'status' => 200,
                'message' => 'Footer retrieved successfully',
                'data' => $footer,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            // validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:footers,name',
                'link' => 'required|url',
                'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'copy_right' => 'required|string|max:255',
            ]);
            // check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }
            // upload the icon if it exists
            // Save image to database
            if ($request->hasFile('icon')) {
                $image_path = $request->file('icon')->store('icons', 'public'); // Store in storage/app/public/images
                $icon_url = asset('storage/' . $image_path); // Convert to URL
            } else {
                $image_url = null;
            }


            // create a new footer
            $footer = Footer::create([
                'name' => $request->name,
                'link' => $request->link,
                'icon' => $icon_url,
                'copy_right' => $request->copy_right,
            ]);
            // return success response
            return response()->json([
                'status' => 201,
                'message' => 'Footer created successfully',
                'data' => $footer,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        try {
            // find the footer by id
            $footer = Footer::find($id);
            // check if footer exists
            if (!$footer) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Footer not found',
                ], 404);
            }
            // return success response
            return response()->json([
                'status' => 200,
                'message' => 'Footer retrieved successfully',
                'data' => $footer,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            // find the footer by id
            $footer = Footer::find($id);
            // check if footer exists
            if (!$footer) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Footer not found',
                ], 404);
            }
            // validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'link' => 'required|url',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'copy_right' => 'required|string|max:255',
            ]);
            // check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }
            // find the footer by id

            // update the footer
            $footer->name = $request->name;
            $footer->link = $request->link;
            $footer->copy_right = $request->copy_right;
            // upload the icon if it exists

            if ($request->hasFile('icon')) {
                if ($footer->icon) {
                    Storage::delete($footer->icon);
                }
                $image_path = $request->file('icon')->store('icons', 'public');
                $image_url = asset('storage/' . $image_path); // Convert to URL
                $footer->icon = $image_url;

            }

            if ($footer->isDirty()) {
                $footer->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Product updated successfully',
                    'footer' => $footer
                ], 200);
            } else {
                return response()->json([
                    'status' => 200,
                    'message' => 'No changes detected',
                    'product' => $footer
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $footer = Footer::findOrFail($id);
            $footer->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Footer deleted successfully',
                'data' => $footer,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
