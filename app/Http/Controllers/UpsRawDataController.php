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

        // Extract unique_id from raw data
        $unique_id = explode(' ', $rawData)[0] ?? 'ESP_UNKNOWN';

        // Check if the unique_id already exists in ups_raw table
        $existingRawData = UpsRaw::where('raw_data', 'LIKE', $unique_id . '%')->first();

        if ($existingRawData) {
            $existingRawData->update(['raw_data' => $rawData]);
            $actionRaw = 'updated';
            Log::info('Updated Existing Raw Data in ups_raw:', ['updated_data' => $rawData]);
        } else {
            $upsRaw = UpsRaw::create(['raw_data' => $rawData]);
            $actionRaw = 'created';
            Log::info('Stored New Raw Data in ups_raw:', ['id' => $upsRaw->id]);
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
            'message' => "UPS data $action successfully, raw data $actionRaw successfully",
            'store_response' => $response->json()
        ]);
    }

    private function parseRawData($rawData)
    {
        // Split the raw text into an array using spaces
        $parts = explode(' ', trim($rawData));
    
        if (count($parts) < 10) { // Ensure at least unique_id, voltages, and status bits exist
            Log::error('Invalid data format received', ['raw_data' => $rawData]);
            return null;
        }
    
        // Store status bits as an array (no conversion)
        $statusBitsArray = isset($parts[8]) ? str_split($parts[8]) : array_fill(0, 8, '0');
    
        return [
            'unique_id'             => $parts[0] ?? 'ESP_UNKNOWN',
            'input_voltage'         => isset($parts[1]) ? (float) $parts[1] : 0.0,
            'input_fault_voltage'   => isset($parts[2]) ? (float) $parts[2] : 0.0,
            'output_voltage'        => isset($parts[3]) ? (float) $parts[3] : 0.0,
            'output_current'        => isset($parts[4]) ? (float) $parts[4] : 0.0,
            'output_frequency'      => isset($parts[5]) ? (float) $parts[5] : 50.0,  // Default to 50 Hz
            'battery_voltage'       => isset($parts[6]) ? (float) $parts[6] : 12.0,  // Default 12V
            'temperature'           => isset($parts[7]) ? (float) $parts[7] : 25.0,  // Default 25°C
            'utility_fail'          => $statusBitsArray[0] === '1', // Bit 7
            'battery_low'           => $statusBitsArray[1] === '1', // Bit 6
            'avr_normal'            => $statusBitsArray[2] === '0', // 0 means NORMAL, 1 means AVR
            'ups_failed'            => $statusBitsArray[3] === '1', // Bit 4
            'ups_line_interactive'  => $statusBitsArray[4] === '1', // Bit 3
            'test_in_progress'      => $statusBitsArray[5] === '1', // Bit 2
            'shutdown_active'       => $statusBitsArray[6] === '1', // Bit 1
            'beeper_on'             => $statusBitsArray[7] === '1', // Bit 0
            'charging_status'       => isset($parts[9]) ? (bool) ($parts[9] == '1') : false // Last value for charging status
        ];
    }
    
}
