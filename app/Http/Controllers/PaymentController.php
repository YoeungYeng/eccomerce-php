<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use App\Services\PayPalService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JsonException;


class PaymentController extends Controller
{
    public function created(Request $request)
    {
        try {
            // check validation
            $validation = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validation error',
                    'errors' => $validation->errors(),
                ], 422);
            }
            // check if user is authenticated
            if (!$request->user()) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized',
                ], 401);
            }
            
            
           
           

            return DB::transaction(function () use ($request) {
                $user = $request->user();
                // Create a unique transaction ID
                $transactionId = Str::ulid();
                // Simulate payment processing
                $status = 'completed'; // Simulate success or failure

                $payment = Payments::create([
                    'user_id' => $user->id,
                    
                    'amount' => $request->amount,
                    'status' => $status,
                    'transaction_id' => $transactionId,
                ]);

                return response()->json([
                    "status" => 200,
                    'message' => 'Payment created successfully',
                    'payment' => $payment,
                ], 200);


            });
        } catch (JsonException $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error creating payment',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error creating payment',
                'error' => $e->getMessage(),
            ], 500);
        }

    }




}
