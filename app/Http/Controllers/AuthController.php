<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


use App\Models\User;
use App\Mail\GenericMail;
use App\Helpers\AuthHelper;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validationRules = [
            'name' => 'required',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string|max:15', 
            'password' => 'required|string|min:8',
            'role' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $validationRules);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        $role = Role::where('name', $validated['role'])->first();
        if (!$role) {
            return $this->errorResponse(['role' => 'Provided Role is not available'], 'Provided Role is not available', 422);
        }

        $validated['status'] = 'Active';
        $validated['profile'] = [
            'language' => 'en',
            'image' => '',
            'fcm_token' => '',
            'timezone' => 'Europe/London',
            'email_notify' => '1',
            'sms_notify' => '1',
        ];

        $user = new User($validated);
        if ($user->save()) {
            if ($role) {
                $user->assignRole($role);
            }

            $return_obj = AuthHelper::create_token_obj($user);
            $return_obj = AuthHelper::add_user_info($return_obj, $user);

            return $this->successResponse('', 'User logged in', 200);
        }
        return $this->errorResponse('', 'Provide proper details', 400);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'allowed_roles' => 'required|array',
            'remember_me' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials, request('remember_me'))) {
            // Return a specific unauthorized response
            return $this->errorResponse(null, 'Unauthorized: Invalid credentials', 401);
        }

        $user = $request->user();
        if ($user->status != 'Active') {
            return $this->errorResponse(null, 'Unauthorized: User is not active', 401);
        }

        $return_obj = AuthHelper::create_token_obj($user);
        $return_obj = AuthHelper::add_user_info($return_obj, $user);

        return $this->successResponse($return_obj, '');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->successResponse('', 'Successfully logged out', 200);
    }

    public function check_availability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:email,phone',
            'data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        }

        $validated = $validator->validated();

        // check if email / phone already exist
        $record = User::where($validated['type'], $validated['data'])->first();
        if ($record) {
            return $this->errorResponse([$validated['type'] => 'Not available'], 'Not available', 400);
        } else {
            return $this->successResponse([$validated['type'] => 'Available'], 'Available', 200);
        }

    }

    public function send_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'nullable|string',
            'type' => 'required|string|in:email,phone',
            'data' => 'required|string',
        ]);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        $validated = $validator->validated();

        // check if user exist
        $user = User::where($validated['type'], $validated['data'])->first();
        if (!$user) {
            return $this->errorResponse('', 'User not found', 400);
        }

        if(empty($validated['action'])) $validated['action'] = 'email_verification';
    

        // create 6 digit random number and save it as OTP in DB
        $code = rand(100000, 999999);
        $otp_obj = AuthHelper::get_old_otps($user, $validated['type']);
        $otp_obj[] = [
            'otp' => $code,
            'type' => $validated['type'],
            'data' => $validated['data'],
            'created_at' => Carbon::now(),
            'verified_at' => '',
        ];
        $user->otp = $otp_obj;
        $user->save();

        // send OTP through email / sms
        if ($validated['type'] == 'email') 
        {
            // Send OTP to the user's email
            if($validated['action'] == 'forgot_password')
            {
                $email_data = ['user_name' => $user->full_name, 'otp' => $code,];
                Mail::to($user->email)->send(new GenericMail('forget_password_otp', $email_data));
            }
            else
            {
                $email_data = ['user_name' => $user->full_name, 'otp' => $code,];
                Mail::to($user->email)->send(new GenericMail('email_verification_otp', $email_data));
            }
        } 
        else if ($validated['type'] == 'phone') 
        {
            // Send OTP to the user's phone number
        }

        return $this->successResponse('', 'OTP send on ' . $validated['type'], 200);
    }

    public function verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:email,phone',
            'data' => 'required|string',
            'otp' => 'required|numeric|digits:6',
        ]);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        $validated = $validator->validated();

        // check if user exist
        $user = User::where($validated['type'], $validated['data'])->first();
        if (!$user) {
            return $this->errorResponse('', 'User not found', 400);
        }

        $otp_obj = $user->otp;
        $error = 'Invalid OTP!!!..';
        foreach ($otp_obj as $index => $obj) {
            if ($obj['type'] == $validated['type'] && $obj['data'] == $validated['data']) {
                if ($obj['verified_at'] != '') {
                    $error = 'Already verified';
                } else if ($obj['otp'] != $validated['otp']) {
                    $error = 'Invalid OTP!!!..';
                } else {
                    // have to check OTP expiry
                    $otp_obj[$index]['verified_at'] = Carbon::now();
                    $error = '';
                    break;
                }
            }
        }

        if ($error == '') {
            if ($validated['type'] == 'email') {
                $user->email_verified_at = Carbon::now();
            } else if ($validated['type'] == 'phone') {
                $user->phone_verified_at = Carbon::now();
            }

            $user->otp = $otp_obj;
            $user->save();

            $return_obj = AuthHelper::create_token_obj($user);
            $return_obj = AuthHelper::add_user_info($return_obj, $user);

            return $this->successResponse($return_obj, ucfirst($validated['type']) . ' verified successfully.', 200);
        }

        return $this->errorResponse(['otp' => [$error]], $error, 422);
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:email,phone',
            'data' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation fails', 422);
        $validated = $validator->validated();

        $user = User::where($validated['type'], $validated['data'])->first();
        if (!$user) {
            return $this->errorResponse('', 'User not found', 400);
        }

        $user->update(['password' => bcrypt($validated['password'])]);

        // Delete all tokens for the user
        $user->tokens()->delete();

        return $this->successResponse('', 'Password has been updated', 200);
    }

}
