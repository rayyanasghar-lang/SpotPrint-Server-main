<?php

namespace App\Helpers;

use Twilio\Rest\Client;

class TwilioSMSHelper
{
    /**
     * Send SMS using Twilio.
     *
     * @param string $to
     * @param string $message
     * @return void
     */
    public static function sendSMS($to, $message)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $from = '+12434534634';

        $client = new Client($sid, $token);

        $client->messages->create(
            $to,
            [
                'from' => $from,
                'body' => $message
            ]
        );
    }

    /**
     * Validate phone number format.
     *
     * @param string $phoneNumber
     * @return bool
     */
    public static function validatePhoneNumber($phoneNumber)
    {
        return preg_match('/^\+?\d{7,15}$/', $phoneNumber);
    }
}
