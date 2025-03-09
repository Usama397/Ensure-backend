<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class UpsRawDataController extends Controller
{
    public function processRawData(Request $request)
    {
        // Log the raw data received
        $rawData = $request->getContent();
        Log::info('Received Raw UPS Data:', ['raw' => $rawData]);

        // Example Parsing Logic: Extracting values from the raw string
        $parsedData = $this->parseRawData($rawData);

        // If parsing fails, return an error response
        if (!$parsedData) {
            return response()->json(['error' => 'Invalid data format'], 400);
        }

        // Forward parsed data to the existing `store` API
        $response = Http::post(env('UPS_STORE_API_URL', 'https://app.ensureups.com/api/ups-data/store'), $parsedData);

        // Log response from store API
        Log::info('Forwarded Data to Store API:', ['response' => $response->json()]);

        return response()->json([
            'status' => $response->status(),
            'message' => 'Raw data processed and forwarded successfully',
            'store_response' => $response->json()
        ]);
    }

    private function parseRawData($rawData)
    {
        // Example Parsing (Modify according to the exact format of raw data)
        $parts = explode(' ', trim($rawData));

        if (count($parts) < 8) { // Ensure minimum required fields exist
            return null;
        }

        return [
            'unique_id' => $parts[0] ?? 'ESP_UNKNOWN',
            'input_voltage' => (float) $parts[1] ?? 0.0,
            'input_fault_voltage' => (float) $parts[2] ?? 0.0,
            'output_voltage' => (float) $parts[3] ?? 0.0,
            'output_current' => (int) $parts[4] ?? 0,
            'output_frequency' => (float) $parts[5] ?? 50.0,
            'battery_voltage' => (float) $parts[6] ?? 0.0,
            'temperature' => (float) $parts[7] ?? 0.0,
            'utility_fail' => isset($parts[8]) && $parts[8] == '1',
            'battery_low' => isset($parts[9]) && $parts[9] == '1',
            'avr_normal' => isset($parts[10]) ? $parts[10] == '0' : true,
            'ups_failed' => isset($parts[11]) && $parts[11] == '1',
            'ups_line_interactive' => isset($parts[12]) && $parts[12] == '1',
            'test_in_progress' => isset($parts[13]) && $parts[13] == '1',
            'shutdown_active' => isset($parts[14]) && $parts[14] == '1',
            'beeper_on' => isset($parts[15]) && $parts[15] == '1',
            'charging_status' => isset($parts[16]) && $parts[16] == '1',
        ];
    }
}
