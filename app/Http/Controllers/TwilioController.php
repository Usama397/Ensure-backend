<?php

namespace App\Http\Controllers;

use App\Models\TwilioSetting;
use App\Traits\TwilioSettingsTrait;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\ParticipatingEvent;
use App\Models\Couple;

class TwilioController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            return Datatables::of(TwilioSetting::get())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = $row['id'];
                    $button = '<a class="btn btn-circle btn-xs" href="' . route("twilio.showEdit", ['id' => $id]) . '" title="Edit"><i class="fa fa-edit"></i></a>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('twilio.index');
    }

    public function editSettingsForm($id)
    {
        $data = TwilioSetting::find($id);
        return view('twilio.edit', ['data' => $data]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'account_sid' => 'required',
            'auth_token' => 'required',
            'number' => 'required',
        ]);

        $inputs = $request->all();

        $model = TwilioSetting::find($inputs['id']);

        if(!empty($model)){
            $model->account_sid = $inputs['account_sid'];
            $model->auth_token = $inputs['auth_token'];
            $model->number = $inputs['number'];
            $model->updated_by = Auth::id();
            $model->update();

            return redirect()->route('twilio.index')
                ->with('success', 'Record updated successfully');
        }
    }

    public function receive(Request $request)
    {
        $from = $request->input('From');
        $body = $request->input('Body');

        $couple = Couple::where('female_participant_id', $from)
                ->orWhere('male_participant_id', $from)
                ->first();

        if ($couple && ($couple->male_dress_message_exchanged == 0 || $couple->female_dress_message_exchanged == 0)) {
            if ($from == $couple->female_participant_id) {
                $couple->update(['female_dress_reply' => $body]);
            } else {
                $couple->update(['male_dress_reply' => $body]);
            }
        }
        else
        {
            if ($from == $couple->female_participant_id) {
                $couple->update(['female_match_reply' => $body]);
            } else {
                $couple->update(['male_match_reply' => $body]);
            }
        }
    }

    public function sendTestTwilioSmsForm()
    {
        return view('twilio.send_sms');
    }

    public function sendTestTwilioSms(Request $request)
    {
        $request->validate([
            'number' => 'required',
        ]);
       $inputs = $request->all();
       $to = $inputs['number'];
       $message = "Hi, this is TWILIO test sms";

       $result = TwilioSettingsTrait::sendTwilioSms($to, $message);

       if ($result){
           return redirect()->route('twilio.sendTestSms')
               ->with('success', 'Sms sent successfully');
       } else {
           return redirect()->route('twilio.sendTestSms')
               ->with('warning', 'Something went wrong! Please try again....');
       }
    }
}
