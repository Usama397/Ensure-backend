<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Twilio\Rest\Client;

use App\Traits\TwilioSettingsTrait;

use App\Models\ParticipatingEvent;
use App\Models\Couple;
use App\Models\Participant;
use Illuminate\Support\Carbon;
use Twilio\Twiml;

class MatchMakingCron extends Command
{
    protected $signature = 'matchmaking:cron';
    protected $description = 'Send matchmaking messages and process replies';

    public function handle()
    {
        //start of event sms
        $events = ParticipatingEvent::where('event_start_date_time', '>=', now())->get();

        // $events = ParticipatingEvent::all();
        foreach($events as $event)
        {
            $couples = $event->couples;
            foreach($couples as $couple)
            {   
                // dress code message sent to both
                $femaleParticipant = Participant::find($couple->female_participant_id);
                $maleParticipant = Participant::find($couple->male_participant_id);

                if($couple->female_dress_message_sent == 0)
                {
                    $to = $femaleParticipant->phone_number;
                    $message = "Please describe what you are wearing in 1-2 sentences.";

                    $female_message = TwilioSettingsTrait::sendTwilioSms($to, $message);
                    if($female_message)
                    {
                        $couple->update([
                            'female_dress_message_sent' => 1,
                        ]);
                    }
                }

                if($couple->male_dress_message_sent == 0)
                {
                    $to = $maleParticipant->phone_number;
                    $message = "Please describe what you are wearing in 1-2 sentences.";

                    $male_message = TwilioSettingsTrait::sendTwilioSms($to, $message);
                    if($male_message)
                    {
                        $couple->update([
                            'male_dress_message_sent' => 1,
                        ]);
                    }
                }


                if($couple->male_dress_message_exchanged == 0)
                {
                    $to = $maleParticipant->phone_number;
                    $message = $couple->female_dress_reply;

                    $male_message = TwilioSettingsTrait::sendTwilioSms($to, $message);
                    if($male_message)
                    {
                        $couple->update([
                            'male_dress_message_exchanged' => 1,
                        ]);
                    }

                }

                if($couple->female_dress_message_exchanged == 0)
                {
                    $to = $femaleParticipant->phone_number;
                    $message = $couple->male_dress_reply;

                    $female_message = TwilioSettingsTrait::sendTwilioSms($to, $message);
                    if($female_message)
                    {
                        $couple->update([
                            'female_dress_message_exchanged' => 1,
                        ]);
                    }   
                }

                $startDateTime = Carbon::parse($event->event_start_date_time);
                $eightMinutesAfterStart = $startDateTime->copy()->addMinutes(8);

                if (now()->greaterThanOrEqualTo($eightMinutesAfterStart) && ($couple->male_match_reply == null || $couple->female_match_reply == null)) {
                    $to = $maleParticipant->phone_number;
                    $message = "Would you like to match with your date.";

                    $male_message = TwilioSettingsTrait::sendTwilioSms($to, $message);
                    
                    
                    $to = $femaleParticipant->phone_number;
                    $message = "Would you like to match with your date.";

                    $female_message = TwilioSettingsTrait::sendTwilioSms($to, $message);
                }


                if(($couple->female_match_reply == 'Y' || $couple->female_match_reply == 'y') && ($couple->male_match_reply == 'Y' || $couple->male_match_reply == 'y'))
                {
                    $to = $maleParticipant->phone_number;
                    $message = "Congratulations! You matched with ".$femaleParticipant->name.", who has described herself wearing ".$couple->female_dress_reply.". Her phone number is ".$femaleParticipant->phone_number.". Happy Dating <3";

                    $male_message = TwilioSettingsTrait::sendTwilioSms($to, $message);
                    
                    
                    $femaleParticipant = Participant::find($couple->female_participant_id);
                    $to = $femaleParticipant->phone_number;
                    $message = "Congratulations! You matched with ".$maleParticipant->name.", who has described himself wearing ".$couple->male_dress_reply.". His phone number is ".$maleParticipant->phone_number.". Happy Dating <3";

                    $female_message = TwilioSettingsTrait::sendTwilioSms($to, $message);

                    if($male_message && $female_message)
                    {
                        $couple->update([
                            'numbers_exchanged' => 1,
                        ]);
                    }   
                }

                

            }
        }
        dd("done");
    }
}
