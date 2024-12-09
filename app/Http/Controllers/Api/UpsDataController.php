<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\UpsData;
use App\Models\UpsSpecification;
use App\Models\DeviceCharging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class UpsDataController extends Controller
{
    // Fetch all UPS data, optionally filtered by user or date range
    public function index(Request $request)
    {
        $query = UpsData::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('timestamp', [$request->input('start_date'), $request->input('end_date')]);
        }

        $upsData = $query->get();

        return response()->json([
            'status' => 200,
            'data' => $upsData,
        ]);
    }

    public function show($id)
    {
        $upsData = UpsData::where('app_user_id', $id)->first();

        if (!$upsData) {
            return response()->json([
                'status' => 404,
                'message' => 'UPS data not found.',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $upsData,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_id' => 'required|string',
            'user_id' => 'required|integer',
            'input_voltage' => 'required|numeric',
            'input_fault_voltage' => 'required|numeric',
            'output_voltage' => 'required|numeric',
            'output_current' => 'required|numeric',
            'output_frequency' => 'required|numeric',
            'battery_voltage' => 'required|numeric',
            'temperature' => 'required|numeric',
            'utility_fail' => 'required|boolean',
            'battery_low' => 'required|boolean',
            'avr_normal' => 'required|boolean',
            'ups_failed' => 'required|boolean',
            'ups_line_interactive' => 'required|boolean',
            'test_in_progress' => 'required|boolean',
            'shutdown_active' => 'required|boolean',
            'beeper_on' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $upsData = UpsData::where('unique_id', $request->unique_id)->where('user_id', $request->user_id)->first();

        if ($upsData) {
            // Update existing record
            $upsData->update($validator->validated());
            return response()->json([
                'status' => 200,
                'message' => 'UPS data updated successfully',
                'data' => $upsData,
            ]);
        } else {
            // Create new record
            $upsData = UpsData::create($validator->validated());
            return response()->json([
                'status' => 201,
                'message' => 'UPS data created successfully',
                'data' => $upsData,
            ]);
        }
    }

    public function findUniqueId(Request $request)
    {
        $request->validate([
            'unique_id' => 'required|string',
        ]);

        $upsData = UpsData::where('unique_id', $request->unique_id)->first();

        if ($upsData) {
            $upsData->app_user_id = auth()->id();
            $upsData->save();

            $upsSpecification = UpsSpecification::where('unique_id', $request->unique_id)->first();

            if ($upsSpecification) {
                $upsSpecification->app_user_id = auth()->id();
                $upsSpecification->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => "User's unique found."
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unique ID not found.'
            ], 404);
        }
    }

    public function chargingStatus(Request $request)
    {
        $userId = Auth::id();
        //$userId = 1;

        /*if (!$userId) {
            return response()->json(['error' => 'User not found.'], 404);
        }*/

        $upsData = UpsData::query()
            ->where('app_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$upsData) {
            return response()->json(['error' => 'No data found for the specified user.'], 404);
        }

        $batteryVoltage = $upsData->battery_voltage;
        $inputVoltage = $upsData->input_voltage;
        $outputCurrent = $upsData->output_current;
        $percentage = $upsData->percentage ?? 0;

        // Calculate percentage if not available
        $percentage = $upsData->percentage ?? $this->calculatePercentage($batteryVoltage);

        $charging = $inputVoltage > 0 && $outputCurrent > 0;

        if ($charging) {
            $soc = 0.4631 * $batteryVoltage - 5.468;
        } else {
            if ($percentage <= 0) {
                return response()->json(['error' => 'Percentage required for discharging SOC calculation.'], 400);
            }
            $soc = (0.4631 * pow($batteryVoltage, 2) - 5.1578 * $batteryVoltage + 34.737 * $percentage) /
                ($batteryVoltage + 0.25474 * $percentage);
        }

        if ($soc >= 1.0) {
            $soc = 1.0;
        } elseif ($soc <= 0.0) {
            $soc = 0.0;
        } else {
            $octiles = [1.0, 0.875, 0.750, 0.675, 0.500, 0.375, 0.250, 0.125, 0.0];
            $soc = collect($octiles)->sortBy(fn($octile) => abs($octile - $soc))->first();
        }

        return response()->json([
            'soc' => round($soc, 3),
            'percentage' => round($percentage, 2) . ' %',
            'charging' => $charging,
        ]);
    }

    private function calculatePercentage($voltage)
    {
        $minVoltage = 11.0; // Minimum battery voltage (0% charge)
        $maxVoltage = 13.5; // Maximum battery voltage (100% charge)

        return (($voltage - $minVoltage) / ($maxVoltage - $minVoltage)) * 100;
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'push' => 'required|boolean',
            'text' => 'required|boolean',
            'email' => 'required|boolean',
        ]);

        $user_id = Auth::id();
       // $user_id = 1;

        $settings = AppSetting::updateOrCreate(
            ['app_user_id' => $user_id],
            [
                'push' => $request->push,
                'text' => $request->text,
                'email' => $request->email,
            ]
        );

        return response()->json(['message' => 'Settings saved successfully', 'settings' => $settings], 200);
    }

    public function getSettings()
    {
        $user_id = Auth::id();
       // $user_id = 1;
        $settings = AppSetting::where('app_user_id', $user_id)->first();

        if (!$settings) {
            return response()->json(['message' => 'Settings not found'], 404);
        }

        return response()->json(['settings' => $settings], 200);
    }

    public function userSpecificationsStore(Request $request)
    {
        $request->validate([
            'unique_id' => 'required|string',
            'continuous_power' => 'required|string',
            'energy' => 'required|string',
            'dimensions' => 'required|string',
        ]);

        $upsSpecification = UpsSpecification::where('unique_id', $request->unique_id)->first();

        if ($upsSpecification) {
            $upsSpecification->update([
                'continuous_power' => $request->continuous_power,
                'energy' => $request->energy,
                'dimensions' => $request->dimensions,
            ]);

            $message = 'UPS Specification updated successfully';
        } else {
            $upsSpecification = UpsSpecification::create([
                'unique_id' => $request->unique_id,
                'continuous_power' => $request->continuous_power,
                'energy' => $request->energy,
                'dimensions' => $request->dimensions,
            ]);

            $message = 'UPS Specification saved successfully';
        }

        return response()->json([
            'message' => $message,
            'data' => $upsSpecification,
        ], 200);
    }

    public function userSpecifications(Request $request)
{
    // Retrieve optional filters from query parameters
    $query = UpsSpecification::query();

    if ($request->has('unique_id')) {
        $query->where('unique_id', $request->unique_id);
    }

    // Get the filtered results or all records
    $upsSpecifications = $query->get();

    // Check if data exists
    if ($upsSpecifications->isEmpty()) {
        return response()->json([
            'status' => 404,
            'message' => 'No UPS specifications found for the given criteria.',
            'data' => [],
        ]);
    }

    return response()->json([
        'status' => 200,
        'message' => 'UPS specifications retrieved successfully.',
        'data' => $upsSpecifications,
    ]);
}


    public function devicechargingStore(Request $request)
    {
        $request->validate([
            'serial_key' => 'required|string',
            'charging_start_time' => 'required|date_format:Y-m-d H:i:s',
            'charging_end_time' => 'required|date_format:Y-m-d H:i:s',
            'charging_status' => 'required|string',
            'event' => 'required|string',
            'specific_day' => 'required|date_format:Y-m-d',
        ]);
    
        // Check if the record already exists for the given serial_key and specific_day
        $chargingData = DeviceCharging::where('serial_key', $request->serial_key)
            ->where('specific_day', $request->specific_day)
            ->first();
    
        if ($chargingData) {
            // Update existing record
            $chargingData->update([
                'charging_start_time' => $request->charging_start_time,
                'charging_end_time' => $request->charging_end_time,
                'charging_status' => $request->charging_status,
                'event' => $request->event,
            ]);
    
            return response()->json([
                'status' => 200,
                'message' => 'Charging data updated successfully.',
                'data' => $chargingData,
            ]);
        } else {
            // Create a new record
            $chargingData = DeviceCharging::create([
                'serial_key' => $request->serial_key,
                'charging_start_time' => $request->charging_start_time,
                'charging_end_time' => $request->charging_end_time,
                'charging_status' => $request->charging_status,
                'event' => $request->event,
                'specific_day' => $request->specific_day,
            ]);
    
            return response()->json([
                'status' => 201,
                'message' => 'Charging data created successfully.',
                'data' => $chargingData,
            ]);
        }
    }

    public function history(Request $request)
{
    // Retrieve optional filters from query parameters
    $query = DeviceCharging::query();

    if ($request->has('serial_key')) {
        $query->where('serial_key', $request->serial_key);
    }

    if ($request->has('specific_day')) {
        $query->where('specific_day', $request->specific_day);
    }

    // Get the filtered results or all records
    $chargingData = $query->get();

    // Check if data exists
    if ($chargingData->isEmpty()) {
        return response()->json([
            'status' => 404,
            'message' => 'No charging history found for the given criteria.',
            'data' => [],
        ]);
    }

    return response()->json([
        'status' => 200,
        'message' => 'Charging history retrieved successfully.',
        'data' => $chargingData,
    ]);
}

    
}
