<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\orders;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function index(){
        $order = orders::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 200,
            'message' => 'All orders',
            'data' => $order
        ]);
    }
    // get order details
    public function show($id){
        $order = orders::with('item', 'item.product')->find($id);

        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found'
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Order details',
            'data' => $order
        ]);
    }

    // update order status
    public function updateOrder($id, Request $request){
        $order = orders::find($id);
        if (!$order) {
            return response()->json([
                'status' => 404,
                'message' => 'Order not found'
            ]);
        }
        
        $order->status = $request->status;
        $order->payment_status = $request->payment_status;
        $order->save();


        return response()->json([
            'status' => 200,
            'message' => 'Order update successfully❤️❤️',
            'data' => $order
        ]);
    }
}
