<?php

namespace App\Helpers;

use Carbon\Carbon;


class AuthHelper
{
    public static function create_token_obj($user)
    {
        $token_name = 'PAT-UserID:' . $user->id . '-RoleName:' . $user->role;
        $tokenResult = $user->createToken($token_name);
        $token = $tokenResult->plainTextToken;

        $return_obj = [
            'accessToken' => $token,
            'token_type' => 'Bearer',
        ];

        return $return_obj;
    }

    public static function add_user_info($obj, $user)
    {
        $obj['userData'] = $user->toArray();
        $obj['userAbilityRules'] = $user->getAllPermissions()->pluck('name'); // 'all.manage';
        return $obj;
    }

    public static  function get_old_otps($user, $type)
    {
        $otp_obj = $user->otp;
        if (empty($otp_obj)) {
            return $otp_obj;
        }

        $new_otp_obj = [];
        foreach ($otp_obj as $obj) {
            if ($type == $obj['type'] && $obj['verified_at'] != '') {
                continue;
            }

            $new_otp_obj[] = $obj;
        }
        return $new_otp_obj;
    }

}
