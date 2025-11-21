<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\System;


class CartController extends Controller
{
    private function updatePriceDetailObj($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        $subtotal = 0;
        $tax = 0;
        $total = 0;

        foreach ($orderItems as $item) {
            $priceDetail = is_array($item->price_detail) ? $item->price_detail : json_decode($item->price_detail, true);

            $subtotal += isset($priceDetail['total_cost']) ? floatval($priceDetail['total_cost']) : 0;
            $tax += isset($priceDetail['tax_amount']) ? floatval($priceDetail['tax_amount']) : 0;
            $total += isset($priceDetail['total_price']) ? floatval($priceDetail['total_price']) : 0;
        }

        // Discount logic placeholder
        $discount = 0;

        $order->price_detail = [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'discount' => round($discount, 2),
            'total' => round($total, 2),
        ];
        $order->total = $total;
        $order->save();

        return $order;
    }

    public function addToCart(Request $request)
    {
        $validationRules = [
            'suid' => 'nullable',
            'user_id' => 'nullable', // User

            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'options' => 'required',
            'price_detail' => 'required', // Should contain price data for the product
            'total' => 'required|numeric', // Total for the product being added
        ];
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        $validated = $validator->validated();

        // check if product available
        $product = Product::where('id', $validated['product_id'])->first();
        if (!$product) {
            return $this->errorResponse('', 'Product Not found', 422);
        }

        $user = auth()->user();
        $userId = $validated['user_id'] ?? ($user ? $user->id : null);
        $suid = !empty($validated['suid']) ? $validated['suid'] : '';

        // get current cart of the user
        $query = Order::where('type', 'Cart');
        if ($userId) {
            $query->where('user_id', $userId); // Changed to use $userId
        } elseif (!$suid) {
            $query->where('session_id', $suid);
        }
        $order = $query->first();

        // If no cart exists, create a new order
        if (!$order) {
            $order = Order::create([
                'type' => 'Cart',
                'user_id' => $userId, // Changed to use $userId here
                'session_id' => Order::generateSessionId(),
                'order_number' => Order::generateOrderNumber(),
                'price_detail' => [], // Initialize as empty array
                'total' => 0,
                'order_status' => 'Pending',
                'payment_status' => 'Unpaid',
            ]);
        }

        // Check if the product already exists in the order_items table
        $existingOrderItem = OrderItem::where('order_id', $order->id)
            ->where('product_id', $validated['product_id'])
            ->where('selected_options', $validated['options']) // Ensure options are compared correctly
            ->first();

        //return [$validated['options'], $existingOrderItem];
        if ($existingOrderItem) {
            return $this->infoResponse('', 'Product already exists in the cart with the same options.');
        }

        // Update the order with the new total and price details
        $order->total += $validated['total'];
        $order->save();

        // Create or update order item
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'selected_options' => $validated['options'],
            'total_price' => $validated['total'],
            'price_detail' => $validated['price_detail'], // Can include the price breakdown
            'product_snapshot' => $product, // Optionally save the product snapshot here
        ]);

        $order = $this->updatePriceDetailObj($order);
        $data = ['order' => $order, 'orderItem' => $orderItem];

        return $this->successResponse($data, 'Product added to cart successfully.', 200);
    }

    public function getCart(Request $request)
    {
        $validationRules = [
            'suid' => 'nullable',
        ];
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        $user = auth('sanctum')->user();
        $suid = $validated['suid'] ?? '';

        // Check if there is already an existing cart for this session_id or user
        $query = Order::where('type', 'Cart')
            ->where(function ($q) use ($user, $suid) {
                if ($user) $q->where('user_id', $user->id);
                if (!empty($suid)) $q->orWhere('session_id', $suid);
            });

        $order = $query->first();
        if (!$order) return $this->successResponse('', '', 200);

        // If the user is logged in, associate the cart with the user
        if (empty($order->user_id) && $user) {
            $order->user_id = $user->id;
            $order->save();
        }

        $orderItems = OrderItem::where('order_id', $order->id)->get();
        $data = ['order' => $order, 'orderItems' => $orderItems];

        return $this->successResponse($data, '', 200);
    }

    public function getOrderItem($id)
    {
        $orderItem = OrderItem::find($id);
        if (!$orderItem) {
            return $this->errorResponse(null, 'Order Item not found', 404);
        }

        return $this->successResponse($orderItem, '', 200);
    }

    public function updateCartItem(Request $request, $orderItemId)
    {
        $validationRules = [
            'action' => 'nullable', // quantity, files, selected_options
            'quantity' => 'nullable|integer|min:0',
            'files' => 'nullable',
            'selected_options' => 'nullable',
            'total_price' => 'nullable',
            'price_detail' => 'nullable',
        ];
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        $action = $validated['action'] ?? 'quantity';
        $orderItem = OrderItem::findOrFail($orderItemId);

        // Fetch the associated order and check conditions
        $order = Order::where('id', $orderItem->order_id)->where('type', 'Cart')->first();
        if (!$order) {
            return $this->errorResponse('Invalid order or order type is not Cart.', 400);
        }

        if (empty($order->user_id) && auth()->check()) {
            $order->user_id = auth()->id();
        }

        if ($action == 'quantity') 
        {
            if ($validated['quantity'] > 0) {
                return $this->infoResponse('', 'Update quantity is not possible at the moment');
            }

            $order->total -= $orderItem->total_price;
            $orderItem->delete();
            $return_msg = 'Cart item removed successfully.';
        } 
        else if ($action == 'files') 
        {
            $orderItem->files = $validated['files'];
            $orderItem->save();

            $return_msg = '';
        } 
        else if ($action == 'selected_options') 
        {
            $order->total -= $orderItem->total_price;

            $orderItem->selected_options = $validated['selected_options'];
            $orderItem->total_price = $validated['total_price'];
            $orderItem->price_detail = $validated['price_detail'];
            $orderItem->save();

            $order->total += $validated['total_price'];
            $return_msg = 'Product Options updated';
        }
        $order->save();

        // create an complete cart object
        $order = $this->updatePriceDetailObj($order);
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        $data = ['order' => $order, 'orderItems' => $orderItems];

        return $this->successResponse($data, $return_msg, 200);
    }

}
