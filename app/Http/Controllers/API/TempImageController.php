<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;


class TempImageController extends Controller
{
    // index
    public function index(){
        return TempImage::all();
    }

    
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:png,jpg,jpeg, JP,gif|max:5000'
            ]);

            // If validation fails
            if ($validator->fails()) {
                return response([
                    "message" => $validator->errors(),
                    "status" => 400
                ]);
            }

            // Store image in the database
            $tempImage = new TempImage();
            $tempImage->name = "Yoeung Yeng";  // Temp initial name❤️
            $tempImage->save();

            // Retrieve and store the uploaded image
            $image = $request->file('image');  // Correct method call
            $imageName = time() . '.' . $image->extension();  // Correct file naming
            $image->move(public_path('storage/temp'), $imageName);

            // Update the TempImage record with the actual image name
            $tempImage->name = $imageName;
            $tempImage->save();



            // Create and save image thumbnail
            $manager = new ImageManager(Driver::class);  // Correct driver initialization

            $img = $manager->read(public_path('storage/temp/' . $imageName));  // Read the uploaded image
            $img->resize(400, 450);
            $img->save(public_path("storage/tumb/") . $imageName);  // Save resized thumbnail

            return response()->json([
                "status" => 200,
                "message" => "Image uploaded successfully ❤️😊",
                "data" => $imageName
            ]);

        } catch (Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }
}
