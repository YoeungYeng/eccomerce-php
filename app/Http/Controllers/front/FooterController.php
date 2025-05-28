<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use Exception;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    // get all footer
    public function getAllFooter()
    {
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
}
