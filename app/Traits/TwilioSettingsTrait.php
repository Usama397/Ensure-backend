<?php

namespace App\Traits;

use App\Models\TwilioSetting;
use Twilio\Rest\Client;

trait TwilioSettingsTrait
{
    public static function sendTwilioSms($to, $message)
    {
        $data =  TwilioSetting::first();

        $twilioNumber = $data->number;
        $twilioAccountSid = $data->account_sid;
        $twilioAuthToken = $data->auth_token;

        $twilio = new Client($twilioAccountSid, $twilioAuthToken);

        $twilio->messages->create(
            '+'.$to,
            [
                'from' => $twilioNumber,
                'body' => $message,
            ]
        );
        return true;
    }
}
