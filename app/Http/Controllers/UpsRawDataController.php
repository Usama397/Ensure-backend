<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\UpsData;
use App\Models\UpsRaw;

class UpsRawDataController extends Controller
{
    private $storeApiUrl = "https://app.ensureups.com/api/ups-data/store"; // External API

    public function processRawData(Request $request)
    {
        // Get the raw data from the request (plain text)
        $rawData = trim($request->getContent());
        Log::info('Received Raw UPS Data:', ['raw_data' => $rawData]);

        // Validate raw data is not empty
        if (empty($rawData)) {
            Log::error('Received empty raw data');
            return response()->json(['error' => 'Empty raw data'], 400);
        }

        // Store raw data in ups_raw table
        try {
            $upsRaw = UpsRaw::create(['raw_data' => $rawData]);
            Log::info('Stored Raw Data in ups_raw:', ['id' => $upsRaw->id]);
        } catch (\Exception $e) {
            Log::error('Failed to store raw UPS data', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Database insert failed'], 500);
        }

        // Extract and parse the raw data
        $parsedData = $this->parseRawData($rawData);

        // If parsing fails, return an error response
        if (!$parsedData) {
            Log::error('Parsing failed for raw data', ['raw_data' => $rawData]);
            return response()->json(['error' => 'Invalid data format'], 400);
        }

        // Check if the unique_id already exists in ups_data
        $existingUpsData = UpsData::where('unique_id', $parsedData['unique_id'])->first();

        if ($existingUpsData) {
            $existingUpsData->update($parsedData);
            $action = 'updated';
            Log::info('Updated Existing UPS Data:', ['updated_data' => $parsedData]);
        } else {
            UpsData::create($parsedData);
            $action = 'created';
            Log::info('Created New UPS Data:', ['new_data' => $parsedData]);
        }

        // Forward parsed data to store API
        $response = Http::post($this->storeApiUrl, $parsedData);

        return response()->json([
            'status' => $action == 'created' ? 201 : 200,
            'message' => "UPS data $action successfully",
            'store_response' => $response->json()
        ]);
    }

    private function parseRawData($rawData)
    {
        // Split the raw text into an array using spaces
        $parts = explode(' ', trim($rawData));
    
        if (empty($parts) || count($parts) < 2) { // Ensure at least unique_id is received
            Log::error('Invalid data format received', ['raw_data' => $rawData]);
            return null;
        }
    
        return [
            'unique_id'             => $parts[0] ?? 'ESP_UNKNOWN',
            'input_voltage'         => isset($parts[1]) ? (float) $parts[1] : 0.0,
            'input_fault_voltage'   => isset($parts[2]) ? (float) $parts[2] : 0.0,
            'output_voltage'        => isset($parts[3]) ? (float) $parts[3] : 0.0,
            'output_current'        => isset($parts[4]) ? (float) $parts[4] : 0.0,
            'output_frequency'      => isset($parts[5]) ? (float) $parts[5] : 50.0,  // Default to 50 Hz
            'battery_voltage'       => isset($parts[6]) ? (float) $parts[6] : 12.0,  // Default 12V
            'temperature'           => isset($parts[7]) ? (float) $parts[7] : 25.0,  // Default 25°C
            'utility_fail'          => isset($parts[8]) ? (bool) ($parts[8] == '1') : false,
            'battery_low'           => isset($parts[9]) ? (bool) ($parts[9] == '1') : false,
            'avr_normal'            => isset($parts[10]) ? (bool) ($parts[10] == '1') : true,
            'ups_failed'            => isset($parts[11]) ? (bool) ($parts[11] == '1') : false,
            'ups_line_interactive'  => isset($parts[12]) ? (bool) ($parts[12] == '1') : false,
            'test_in_progress'      => isset($parts[13]) ? (bool) ($parts[13] == '1') : false,
            'shutdown_active'       => isset($parts[14]) ? (bool) ($parts[14] == '1') : false,
            'beeper_on'             => isset($parts[15]) ? (bool) ($parts[15] == '1') : false,
            'charging_status'       => isset($parts[16]) ? (bool) ($parts[16] == '1') : false,
        ];
    }
    
}
