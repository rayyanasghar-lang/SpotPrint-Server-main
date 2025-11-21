<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Traits\DataTableTrait;
use App\Models\Order;

class CouponController extends Controller
{
    use DataTableTrait;

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $filters = json_decode(request('filters'), true) ?? [];
        $coupons = Coupon::query();

        $searchColumns = ['code', 'value', 'description', 'max_uses', 'used_count', 'status', 'category'];
        $couponData = $this->dataTable($coupons, $searchColumns, $filters);

        return $this->successResponse($couponData, 'Coupons retrieved successfully', 200);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validationRules = [
            'type' => 'required|string|in:Fixed,Percentage',
            'value' => 'required|numeric|min:0', // Ensure 'value' is numeric.
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($request) {
                    if (isset($request->end_date) && $value >= $request->end_date) {
                        $fail('The start date must be before the end date.');
                    }
                },
            ],
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if (isset($request->start_date) && $value <= $request->start_date) {
                        $fail('The end date must be after the start date.');
                    }
                },
            ],
            'max_uses' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if (isset($request->per_user_limit) && $value < $request->per_user_limit) {
                        $fail('The max uses must be greater than or equal to the per user limit.');
                    }
                },
            ],
            'per_user_limit' => 'required|integer|min:1',
            'status' => 'required|in:Yes,No'
        ];

        $customMessages = [
            'start_date.after_or_equal' => 'The start date must be today or a future date.',
            'type.in' => 'The type must be either Fixed or Percentage.',
            'value.required' => 'The value field is required and should be a number.',
            'end_date.required' => 'The end date field is required.',
            'max_uses.required' => 'The maximum uses field is required and must be an integer greater than or equal to per user limit.',
            'per_user_limit.required' => 'The per user limit is required and should be an integer greater than or equal to 1.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);
        if ($validator->fails())
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        $validated['code'] = $request->filled('code')
        ? strtoupper($request->code)
        : $this->generateCouponCode();
        $coupon = Coupon::create($validated);
        return $this->successResponse($coupon, 'Coupon created successfully.', 201);
    }

    /**
     * @param Request $request
     * @param string $id
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, string $id)
    {
        $couponRec = Coupon::find($id);
        $validationRules = [
            'type' => 'required|string|in:Fixed,Percentage',
            'value' => 'required|numeric|min:0', // Ensure 'value' is numeric.
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($request, $couponRec) {
                    if (isset($request->end_date) && $value >= $request->end_date) {
                        $fail('The start date must be before the end date.');
                    }
                },
            ],
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request, $couponRec) {
                    if ($couponRec->used_count > 0 && $request->end_date < $couponRec->end_date) {
                        $fail('The end date must be greater than the already saved end date if the coupon has been used.');
                    }
                    if (isset($request->start_date) && $value <= $request->start_date) {
                        $fail('The end date must be after the start date.');
                    }
                },
            ],
            'max_uses' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request, $couponRec) {
                    if ($couponRec->used_count > 0 && $request->max_uses < $couponRec->max_uses) {
                        $fail('The max uses must be greater than or equal to the already saved max uses if the coupon has been used.');
                    }
                    if (isset($request->per_user_limit) && $value < $request->per_user_limit) {
                        $fail('The max uses must be greater than or equal to the per user limit.');
                    }
                },
            ],
            'per_user_limit' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request, $couponRec) {
                    if ($couponRec->used_count > 0 && $value < $couponRec->per_user_limit) {
                        $fail('The per user limit must be greater than or equal to the already saved per user limit if the coupon has been used.');
                    }
                },
            ],
            'status' => 'required|in:Yes,No',
            'description' => 'nullable',
        ];

        $customMessages = [
            'start_date.after_or_equal' => 'The start date must be today or a future date.',
            'end_date.required' => 'The end date field is required.',
            'end_date.after' => 'The end date must be after the start date.',
            'type.in' => 'The type must be either Fixed or Percentage.',
            'value.required' => 'The value field is required and should be a number.',
            'max_uses.required' => 'The max uses field is required and must be an integer greater than or equal to per user limit.',
            'per_user_limit.required' => 'The per user limit is required and should be an integer greater than or equal to 1.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);
        if ($validator->fails())
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        $couponRec->update($validated);
        return $this->successResponse(null, 'Coupon updated successfully.', 200);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon)
            return $this->errorResponse(null, 'Coupon not found', 404);

        return $this->successResponse($coupon, 'Coupon retrieved successfully', 200);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon)
            return $this->errorResponse(null, 'Coupon not found', 404);

        if ($coupon->used_count > 0) {
            return $this->errorResponse(null, 'This coupon cannot be deleted because it has already been used.', 400);
        }

        $coupon->delete();
        return $this->successResponse(null, 'Coupon deleted successfully', 200);
    }

    /**
     * @param $length
     * @return string
     */
    private function generateCouponCode($length = 8)
    {
        do {
            // Generate a random string of the specified length
            $couponCode = strtoupper(Str::random((int) $length));

            // Check if the coupon code already exists in the database
            $exists = Coupon::where('code', $couponCode)->exists();
        } while ($exists);

        return $couponCode;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     * Frontend APIs
     */
    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'order_id' => 'required|integer',
            'coupon_code' => 'required|string',
        ]);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        $coupon = Coupon::where('code', $validated['coupon_code'])->first();
        if (!$coupon || !$coupon->isValid()) return $this->errorResponse(null, 'Invalid or expired coupon.', 422);

        $allRedemptionCount = CouponRedemption::where('coupon_id', $coupon->id)->count();
        if ($coupon->max_uses <= $allRedemptionCount) return $this->errorResponse(['code' => ['Code has reached to max use']], 'Validation failed', 422);

        $redemptionCount = CouponRedemption::where('user_id', $validated['user_id'])->where('coupon_id', $coupon->id)->count();
        if ($redemptionCount >= $coupon->per_user_limit) return $this->errorResponse(['code' => ['User has exceeded the coupon usage limit.']], 'Validation failed', 422);

        // Apply the coupon
        DB::transaction(function () use ($validated, $coupon) {
            $CouponRedemption = CouponRedemption::create([
                'coupon_id' => $coupon->id,
                'user_id' => $validated['user_id'],
                'order_id' => $validated['order_id'],
                'redeemed_at' => now(),
            ]);

            $coupon->increment('used_count');


            // Add coupon details to order's price_detail
            $order = Order::find($validated['order_id']);
            if ($order) {
                $priceDetail = is_array($order->price_detail) ? $order->price_detail : json_decode($order->price_detail, true);

                // Calculate discount
                $discount = 0;
                $orderTotal = isset($priceDetail['total']) ? $priceDetail['total'] : $order->total;

                if ($coupon->type === 'Fixed')  $discount = min($coupon->value, $orderTotal);
                elseif ($coupon->type === 'Percentage') $discount = round($orderTotal * ($coupon->value / 100), 2);

                $priceDetail['discount'] = $discount;
                $total_after_discount = max(0, $orderTotal - $discount);

                $priceDetail['coupon'] = [
                    'code' => $coupon->code,
                    'coupon_object' => $coupon,
                    'Coupon_redemption_object' => $CouponRedemption,
                    'coupon_applied_at' => now(),
                ];

                $order->price_detail = $priceDetail;
                $order->total = $total_after_discount; // update order total
                $order->save();
            }
        });

        return $this->successResponse($coupon, 'Coupon applied successfully.', 200);
    }

}
