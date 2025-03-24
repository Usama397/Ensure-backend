<?php

namespace App\Http\Controllers;

use App\Models\UPSChargingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UPSController extends Controller
{
    public function receiveChargingStatus(Request $request)
    {
        $rawData = $request->getContent();
        \Log::info("Raw Data Received: " . $rawData);

        $parts = explode(',', $rawData);
        if (count($parts) < 6) {
            return response()->json(['error' => 'Invalid data format'], 400);
        }

        $data = [
            'serial_key' => trim($parts[0]),
            'charging_start_time' => trim($parts[1]) !== '' ? trim($parts[1]) : null,
            'charging_end_time' => trim($parts[2]) !== '' ? trim($parts[2]) : null,
            'charging_status' => trim($parts[3]),
            'event' => trim($parts[4]),
            'specific_day' => trim($parts[5]),
        ];

        // Save to database: update if serial_key exists, otherwise create
        UPSChargingStatus::updateOrCreate(
            ['serial_key' => $data['serial_key']],
            $data
        );

        // Forward to external API
        $response = Http::post('https://app.ensureups.com/api/device-charging', $data);

        \Log::info("Data Stored & Forwarded: ", $data);
        \Log::info("External API Response: " . $response->body());

        return response()->json([
            'message' => 'Data stored and forwarded successfully',
            'external_response' => $response->json(),
        ], $response->status());
    }
}
