<?php

namespace App\Http\Controllers;

use App\Models\UPSChargingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UPSController extends Controller
{
    public function receiveChargingStatus(Request $request)
    {
        $rawData = $request->getContent();
        // \Log::info("Raw Data Received: " . $rawData);

        $parts = explode(',', $rawData);
        if (count($parts) < 6) {
            return response()->json(['error' => 'Invalid data format'], 400);
        }

        $serialKey = trim($parts[0]);
        $startTime = trim($parts[1]);
        $endTime = trim($parts[2]);
        $chargingStatus = trim($parts[3]);
        $event = trim($parts[4]);
        $specificDay = trim($parts[5]);

        if ($chargingStatus === "Charging Completed" && $event === "Battery Fully Charged") {
            // Step 1: Get the latest Charging Started record
            $lastStartEvent = UPSChargingStatus::where('serial_key', $serialKey)
                ->where('charging_status', 'Charging Started')
                ->where('event', 'Charging')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastStartEvent) {
                // Step 2: Update that record's charging_end_time with this endTime
                $lastStartEvent->charging_end_time = $endTime;
                $lastStartEvent->save();
                // \Log::info("Updated Charging Started record's end_time to: " . $endTime);

                // Step 3: Use its start_time for the Charging Completed record
                $startTime = $lastStartEvent->charging_start_time;
            } else {
                \Log::warning("No previous Charging Started event found for serial_key: " . $serialKey);
            }
        }

        // Save the Charging Completed (or Started) record
        $data = [
            'serial_key' => $serialKey,
            'charging_start_time' => $startTime !== '' ? $startTime : null,
            'charging_end_time' => $endTime !== '' ? $endTime : null,
            'charging_status' => $chargingStatus,
            'event' => $event,
            'specific_day' => $specificDay,
        ];

        UPSChargingStatus::create($data);

        // Forward to external API
        $response = Http::post('https://app.ensureups.com/api/device-charging', $data);

        // \Log::info("Data Stored & Forwarded: ", $data);
        // \Log::info("External API Response: " . $response->body());

        return response()->json([
            'message' => 'Data stored and forwarded successfully',
            'external_response' => $response->json(),
        ], $response->status());
    }
}
