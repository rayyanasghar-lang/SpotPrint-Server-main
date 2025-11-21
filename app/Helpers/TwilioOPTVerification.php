<?php

namespace App\Helpers;

use Exception;
//use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class TwilioOPTVerification
{
    protected $twilio;

    protected $twilioAuthToken;

    protected $twilioSid;

    protected $twilioVerifySid;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->twilioAuthToken = env("TWILIO_AUTH_TOKEN");
        $this->twilioSid = env("TWILIO_SID");
        $this->twilioVerifySid = env("TWILIO_VERIFY_SID");

        $this->twilio = new \Twilio\Rest\Client($this->twilioSid, $this->twilioAuthToken);
    }

    public function getOPT($phone)
    {
        $verification = $this->twilio->verify->v2->services($this->twilioVerifySid)
            ->verifications
            ->create($phone, "sms");

        return $verification;
    }

    public function verifyOPT($array)
    {
        $verification = $this->twilio->verify->v2->services($this->twilioVerifySid)
            ->verificationChecks
            ->create([
                "to" => $array['phone'] ,
                "code" => $array['code']
            ]);

        return $verification;
    }
}
