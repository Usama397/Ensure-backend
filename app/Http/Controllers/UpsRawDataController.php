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
        // Log::info('Received Raw UPS Data:', ['raw_data' => $rawData]);

        // Validate raw data is not empty
        if (empty($rawData)) {
            Log::error('Received empty raw data');
            return response()->json(['error' => 'Empty raw data'], 400);
        }

        // Store raw data in ups_raw table
        try {
            $upsRaw = UpsRaw::create(['raw_data' => $rawData]);
            // Log::info('Stored Raw Data in ups_raw:', ['id' => $upsRaw->id]);
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
            // Log::info('Updated Existing UPS Data:', ['updated_data' => $parsedData]);
        } else {
            UpsData::create($parsedData);
            $action = 'created';
            // Log::info('Created New UPS Data:', ['new_data' => $parsedData]);
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

        if (count($parts) < 10) {
            Log::error('Invalid data format received', ['raw_data' => $rawData]);
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
