<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\EmailOtpVerificationMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthControllerOld extends Controller
{
    public function register(Request $r)
    {
        // return $r->all();
        $validator = Validator::make($r->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'profile' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_type' => 'validation_error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $user = new User($validated);
        // return $user;
        if ($user->save()) {
            $role = Role::where('name', $r->role)->first();
            $user->assignRole($role->name);

            $token = self::auth_token($user);

            // send verification mail
            self::send_otp_email($r);

            return response()->json([
                'auth_token' => $token,
                'user' => $user,
            ],);
        } else {
            return response()->json([
                'error_type' => 'server_error',
                'message' => 'Failed to register',
            ], 400);
        }
    }

    public function login(Request $r)
    {
        $validator = Validator::make($r->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'role' => 'required|string|exists:roles,name',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_type' => 'validation_error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials, request('remember_me'))) { // login
            return response()->json([
                'error_type' => 'unauthorized',
                'message' => 'Failed to login',
            ], 401);
        }

        $user = $r->user(); // get user after login
        $token = self::auth_token($user);

        return response()->json([
            'auth_token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $r)
    {
        $r->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function send_email(Request $r)
    {
        // resend link verification
    }

    public function verify_email(Request $r)
    {
        // link verification
    }

    public function send_otp_email(Request $r)
    {
        // resend email otp verification
        $validator = Validator::make($r->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_type' => 'validation_error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $user = User::where('email', $r->email)->first();
        // create 6 digit random number and save it as OTP in DB
        $code = rand(100000, 999999);
        $otp_obj = $this->get_old_otps($user, 'email');
        $otp_obj[] = [
            'otp' => $code,
            'type' => 'email',
            'data' => $validated['email'],
            'created_at' => Carbon::now(),
            'verified_at' => ''
        ];
        $user->otp = $otp_obj;
        $user->save();

        if (is_null($user->email_verified_at)) {
            Mail::to($user->email)->send(new EmailOtpVerificationMail($user, $code));

            return response()->json([
                'message' => 'OTP sent on email',
            ],);
        } else {
            return response()->json([
                'message' => 'Email already verified',
            ],);
        }

        // return $this->successResponse('', 'OTP send on phone', 200);

    }

    public function verify_otp_email(Request $r)
    {
        // email otp verification
        $validator = Validator::make($r->all(), [
            'email' => 'required|string|email|exists:users,email',
            'otp' => 'required|numeric|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_type' => 'validation_error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $validated = $validator->validated();
        // return $validated;

        $user = User::where('email', $r->email)->first();

        $otp_obj = $user->otp;
        // return [$otp_obj,$validated];

        $error = 'Invalid OTP!!!..';
        foreach ($otp_obj as $index => $obj) {
            if ($obj['type'] == 'email' && $obj['data'] == $validated['email']) {
                if ($obj['verified_at'] != '') $error = 'Already verified';
                else if ($obj['otp'] != $validated['otp']) $error = 'Invalid OTP!!!..';
                else {
                    // have to check OTP expiry
                    $otp_obj[$index]['verified_at'] = Carbon::now();
                    $error = '';
                    break;
                }
            }
        }

        if ($error == '') {
            $user->email_verified_at = Carbon::now();
            $user->otp = $otp_obj;
            $user->save();
            $user->createAsStripeCustomer();

            return response()->json([
                'message' => 'Email verified',
            ],);
        } else {
            return response()->json([
                'message' => 'Email already verified',
            ],);
        }
    }

    public function send_otp(Request $r)
    {
        // resend phone otp verification
    }

    public function verify_otp(Request $r)
    {
        // phone otp verification
    }

    function auth_token($user)
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    private function get_old_otps($user, $type)
    {
        $otp_obj = $user->otp;
        if (empty($otp_obj)) return $otp_obj;

        $new_otp_obj = [];
        foreach ($otp_obj as $obj) {
            if ($type == $obj['type'] && $obj['verified_at'] != '') continue;

            $new_otp_obj[] = $obj;
        }
        return $new_otp_obj;
    }

    function test(Request $r)
    {
        return 1;
    }
}
