<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UpsRaw;

class UpsRawDataController extends Controller
{
    public function processRawData(Request $request)
    {
        // Get the raw data from the request
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

        return response()->json([
            'status' => 201,
            'message' => "Raw data stored successfully",
            'ups_raw_id' => $upsRaw->id,
        ]);
    }
}
