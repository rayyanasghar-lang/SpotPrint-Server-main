<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\DataTableTrait;

class OrderController extends Controller
{
    use DataTableTrait;

    private $validationRules = [
        'user_id'               => 'required|exists:users,id',
        'address_info'          => 'required',
        'coupon_code'           => 'nullable|string',
        'payment_method'        => 'required|string',
        'metadata'              => 'nullable|array',
        'total'                 => 'required|numeric|min:0',
        'order_status'          => 'required|string|in:Pending,Processing,Completed,Cancelled',
        'payment_status'        => 'required|string|in:Unpaid,Paid,Refunded','Cancelled',
        'products'              => 'required|array',
        'products.*.product_id'         => 'required|exists:products,id',
        'products.*.quantity'           => 'required|integer|min:1',
        'products.*.total_price'        => 'required|integer|min:0',
        'products.*.files'              => 'nullable',
        'products.*.selected_options'   => 'nullable',
        'products.*.price_detail'       => 'nullable',
    ];

    public function index(Request $request) // being used on frontend
    {

        $filters = json_decode(request('filters')) ?? [];

        //this is the function to fetch the orders that are not carts (is order an order if it's in a cart?)
        $query   = Order::whereNotNull('id');

        if (strtolower(auth()->user()->role) !== 'admin') {
            $query->where('user_id', auth()->user()->id);
        }

        $searchColumns = ['order_number', 'tracking_number', 'type', 'price_detail', 'payment_status', 'order_status']; // Add user.full_name to search columns
        $orders        = $this->dataTable($query, $searchColumns, $filters);

        return $this->successResponse($orders, 'testing function', 200);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.product', 'couponRedemption.coupon'])->find($id); // Eager load orderItems and product

        if (!$order) {
            return $this->errorResponse(null, 'Order not found', 404);
        }

        // Include product snapshot in each order item
        foreach ($order->orderItems as $orderItem) {
            $orderItem->product_snapshot = $orderItem->product;
        }

        return $this->successResponse($order, 'Order retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        $validated                 = $validator->validated();
        $validated['type']         = 'Order';
        $validated['order_number'] = Order::generateOrderNumber(); // Generate order number
        $order                     = Order::create($validated);

        $orderItems = []; 
        foreach ($validated['products'] as $product) {
            $product['product_snapshot'] = Product::where('id', $product['product_id'])->first();
            $product['order_id'] = $order->id;
            $orderItems[] = OrderItem::create($product);
        }

        $data = ['order' => $order, 'orderItems' => $orderItems];

        return $this->successResponse($data, 'Order created successfully', 201);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (! $order) {
            return $this->errorResponse(null, 'Order not found', 404);
        }

        $validator = Validator::make($request->all(), $this->validationRules);


        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        $validated = $validator->validated();
        $order->update($validated);


        $orderItems = []; 
        OrderItem::where('order_id', $order->id)->delete(); // Delete all previous order items and add new ones
        foreach ($validated['products'] as $product) {
            $product['product_snapshot'] = Product::where('id', $product['product_id'])->first();
            $product['order_id'] = $order->id;
            $orderItems[] = OrderItem::create($product);
        }

        $data = ['order' => $order, 'orderItems' => $orderItems];


        return $this->successResponse($data, 'Order updated successfully', 200);
    }

    public function orderUpdate(Request $request, $id)
    {
        $order = Order::find($id);
        if (! $order) {
            return $this->errorResponse(null, 'Order not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'total' => 'required|integer|min:0',
            'address_info' => 'required',
        ]);


        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        $validated = $validator->validated();
        $order->update($validated);


        return $this->successResponse([], 'Order updated successfully', 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (! $order) {
            return $this->errorResponse(null, 'Order not found', 404);
        }

        $order->delete();
        return $this->successResponse(null, 'Order deleted successfully', 200);
    }

    /**** Frontend APIs ****/
    public function getOrders()
    {
        $userId = auth()->user()->id;
        $orders = Order::with('orderItems') // Eager load the orderItems relationship
            ->where('user_id', $userId)
            ->where('type', 'Order')
            ->orderBy('id', 'desc')
            ->get();

        return $this->successResponse($orders, '', 200);
    }
}
