<?php
namespace App\Http\Controllers;

use App\Mail\GenericMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Stripe\Charge;
use Stripe\Stripe;

class CheckoutController extends Controller
{

    // Update order details like shipping address, billing address, metadata, etc.
    public function updateOrderDetails(Request $request, $orderId)
    {
        $request->validate([
            'address_info' => 'nullable',
            'metadata'     => 'nullable',
        ]);

        $order = Order::findOrFail($orderId);
        if (! $order) {
            return $this->errorResponse(null, 'Order not found', 404);
        }

        if (empty($order->user_id) && auth()->check()) {
            $order->user_id = auth()->id();
        }

        // Update specific fields based on the request.
        if ($request->filled('address_info')) {
            $order->address_info = $request->address_info;
        }
        if ($request->filled('metadata')) {
            $order->metadata = $request->metadata;
        }

        if (! $order->save()) {
            return $this->errorResponse(null, 'Failed to update order details', 500);
        }

        return $this->successResponse($order, 'Order details updated successfully.', 200);
    }

    // 6. Update order status or payment status.
    public function updateOrderStatus(Request $request, $orderId)
    {
        $request->validate([
            'order_status'   => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        $order = Order::find($orderId);
        if (! $order) {
            return $this->errorResponse(null, 'Order not found', 404);
        }

        $user = User::find($order->user_id);
        if (! $user) {
            return $this->errorResponse(null, 'User not found', 404);
        }

        if ($request->filled('order_status')) {
            $order->order_status = $request->order_status;
        }
        if ($request->filled('payment_status')) {
            //$order->payment_status = $request->payment_status; // Payment status should ideally be updated via payment gateway callbacks only.
        }

        $order->save();
        $this->logOrderStatus($order, $request->order_status); // Log status changes.

        $email_data = ['user_name' => $user->full_name, 'order_id' => $orderId, 'order_status' => $order->order_status];
        Mail::to($user->email)->send(new GenericMail('order_status_update', $email_data));

        return $this->successResponse($order, 'Order status updated successfully.', 200);
    }

    // Private method to log order status changes.
    private function logOrderStatus($order, $orderStatus)
    {
        $existingLogs = is_array($order->status_logs) ? $order->status_logs : json_decode($order->status_logs, true) ?? [];

        $existingLogs[] = [
            'status'    => $orderStatus,
            'date_time' => now(),       
        ];

        $order->status_logs = $existingLogs;
        $order->save();
    }

    // Function to handle payment processing through Stripe (integration to be added).
    public function processPayment(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'amount'   => 'required|integer|min:1', // Amount in cents
            'currency' => 'required|string|max:3',
            'source'   => 'required|string', // Token received from frontend
        ]);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        $order = Order::find($orderId);
        if (! $order) {
            return $this->errorResponse(null, 'Order not found', 404);
        }

        if ($order->payment_status == 'Paid') {
            return $this->errorResponse(null, 'Order already paid', 400);
        }

        $user_id = $order->user_id;
        if(empty($user_id)) $user_id = auth()->user()->id;
        $user = User::find($user_id);
        if (! $user) {
            return $this->errorResponse('', 'User not found !', 404);
        }

        $error_res = ['obj' => '', 'message' => '', 'code' => ''];
        try {
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            // Create the charge
            $charge = Charge::create([
                'amount'      => $validated['amount'],
                'currency'    => $validated['currency'],
                'source'      => $validated['source'],
                'description' => 'Payment for Order #' . $orderId,
            ]);

            if ($charge['status'] != 'succeeded') {
                return $this->errorResponse($charge, 'Payment Failed');
            }

            $order->type            = 'Order';
            $order->user_id         = $user_id;
            $order->order_status    = 'Confirmed';
            $order->payment_status  = 'Paid';
            $order->payment_method  = 'Stripe';
            $order->transaction_id  = '';
            $order->payment_details = $charge;
            $order->created_at = now(); // Update created_at to now in case the cart was created days ago
            $order->save();

            $email_data = ['user_name' => $user->full_name, 'order_id' => $orderId, 'order_status' => $order->order_status];
            Mail::to($user->email)->send(new GenericMail('order_status_update', $email_data));

            return $this->successResponse($charge, 'Payment completed successfully', 200);
        } catch (\Stripe\Exception\CardException $e) {
            // Error details for card-related issues
            $errorDetails = [
                'status'  => $e->getHttpStatus(),
                'type'    => $e->getError()->type,
                'code'    => $e->getError()->code,
                'param'   => $e->getError()->param,
                'message' => $e->getError()->message,
            ];
            Log::error('CardException:', $errorDetails);
            //return $this->errorResponse($e->getMessage(), 'Card payment failed.', 400);
            $error_res = ['obj' => $errorDetails, 'message' => 'Card payment failed.', 'code' => 400];
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Too many requests made to the API too quickly
            Log::error('RateLimitException:', ['message' => $e->getMessage()]);
            //return $this->errorResponse($e->getMessage(), 'Too many requests. Please try again later.', 429);
            $error_res = ['obj' => $e->getMessage(), 'message' => 'Card payment failed.', 'code' => 400];

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
            Log::error('InvalidRequestException:', ['message' => $e->getMessage()]);
            //return $this->errorResponse($e->getMessage(), 'Invalid payment parameters.', 400);
            $error_res = ['obj' => $e->getMessage(), 'message' => 'Invalid payment parameters.', 'code' => 400];

        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            Log::error('AuthenticationException:', ['message' => $e->getMessage()]);
            //return $this->errorResponse($e->getMessage(), 'Payment authentication failed.', 401);
            $error_res = ['obj' => $e->getMessage(), 'message' => 'Payment authentication failed.', 'code' => 401];

        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            Log::error('ApiConnectionException:', ['message' => $e->getMessage()]);
            //return $this->errorResponse($e->getMessage(), 'Network error. Please try again.', 503);
            $error_res = ['obj' => $e->getMessage(), 'message' => 'Network error. Please try again.', 'code' => 503];

        } catch (\Stripe\Exception\ApiErrorException $e) {
            // General API error
            Log::error('ApiErrorException:', ['message' => $e->getMessage()]);
            //return $this->errorResponse($e->getMessage(), 'Payment processing error.', 500);
            $error_res = ['obj' => $e->getMessage(), 'message' => 'Payment processing error.', 'code' => 500];

        } catch (\Exception $e) {
            // Any other unexpected errors
            Log::error('Exception:', ['message' => $e->getMessage()]);
            //return $this->errorResponse($e->getMessage(), 'An unexpected error occurred.', 500);
            $error_res = ['obj' => $e->getMessage(), 'message' => 'An unexpected error occurred.', 'code' => 500];
        }

        return $this->errorResponse($error_res['obj'], $error_res['message'], $error_res['code']);
    }
}
