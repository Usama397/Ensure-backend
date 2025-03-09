<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\UpsData;
use App\Models\UpsRaw;

class UpsRawDataController extends Controller
{
    private $storeApiUrl = "https://app.ensureups.com/api/ups-data/store";

    public function processRawData(Request $request)
    {
        // Get the raw data from the request
        $rawData = $request->getContent();
        Log::info('Received Raw UPS Data:', ['raw' => $rawData]);

        // Extract unique_id from raw data
        $parsedData = $this->parseRawData($rawData);
        $uniqueId = $parsedData['unique_id'] ?? 'ESP_UNKNOWN';

        // Store the raw data in the `ups_raw` table
        UpsRaw::create([
            'unique_id' => $uniqueId,
            'raw_data' => $rawData
        ]);

        Log::info('Stored Raw UPS Data:', ['unique_id' => $uniqueId, 'raw' => $rawData]);

        // If parsing fails, return an error response
        if (!$parsedData) {
            return response()->json(['error' => 'Invalid data format'], 400);
        }

        // Check if a record exists for this unique_id
        $existingUpsData = UpsData::where('unique_id', $parsedData['unique_id'])->first();

        if ($existingUpsData) {
            // Update the existing record
            $existingUpsData->update($parsedData);
            $action = 'updated';
            Log::info('Updated Existing UPS Data:', ['updated_data' => $parsedData]);
        } else {
            // Create a new record
            $existingUpsData = UpsData::create($parsedData);
            $action = 'created';
            Log::info('Created New UPS Data:', ['new_data' => $parsedData]);
        }

        // Forward data to store API
        $response = Http::post($this->storeApiUrl, $parsedData);

        return response()->json([
            'status' => $existingUpsData->wasRecentlyCreated ? 201 : 200,
            'message' => "UPS data $action successfully",
            'store_response' => $response->json()
        ]);
    }

    private function parseRawData($rawData)
    {
        $parts = explode(' ', trim($rawData));

        if (count($parts) < 10) {
            return null;
        }

        return [
            'unique_id' => $parts[0] ?? 'ESP_UNKNOWN',
            'input_voltage' => (float) $parts[1] ?? null,
            'input_fault_voltage' => (float) $parts[2] ?? null,
            'output_voltage' => (float) $parts[3] ?? null,
            'output_current' => (float) $parts[4] ?? null,
            'output_frequency' => (float) $parts[5] ?? null,
            'battery_voltage' => (float) $parts[6] ?? null,
            'temperature' => (float) $parts[7] ?? null,
            'utility_fail' => isset($parts[8]) && $parts[8] == '1',
            'battery_low' => isset($parts[9]) && $parts[9] == '1',
            'avr_normal' => isset($parts[10]) && $parts[10] == '1',
            'ups_failed' => isset($parts[11]) && $parts[11] == '1',
            'ups_line_interactive' => isset($parts[12]) && $parts[12] == '1',
            'test_in_progress' => isset($parts[13]) && $parts[13] == '1',
            'shutdown_active' => isset($parts[14]) && $parts[14] == '1',
            'beeper_on' => isset($parts[15]) && $parts[15] == '1',
            'charging_status' => isset($parts[16]) && $parts[16] == '1',
        ];
    }
}
