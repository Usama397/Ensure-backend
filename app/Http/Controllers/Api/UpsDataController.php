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

    use App\Models\UpsData;
    use App\Models\UpsDataLog;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    
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
    
        $upsData = UpsData::where('unique_id', $request->unique_id)
            ->where('user_id', $request->user_id)
            ->first();
    
        if ($upsData) {
            // Update existing record
            $upsData->update($validator->validated());
    
            // Log the update
            UpsDataLog::create([
                'ups_data_id' => $upsData->id,
                'user_id' => $request->user_id,
                'data' => json_encode($validator->validated()), // Store the updated data
                'action' => 'updated',
            ]);
    
            return response()->json([
                'status' => 200,
                'message' => 'UPS data updated successfully',
                'data' => $upsData,
            ]);
        } else {
            // Create new record
            $upsData = UpsData::create($validator->validated());
    
            // Log the creation
            UpsDataLog::create([
                'ups_data_id' => $upsData->id,
                'user_id' => $request->user_id,
                'data' => json_encode($validator->validated()), // Store the new data
                'action' => 'created',
            ]);
    
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

        // Find all DeviceCharging records by unique_id (mapped to serial_key)
        $affectedRows = DeviceCharging::where('serial_key', $request->unique_id)
            ->update(['app_user_id' => auth()->id()]); // Set app_user_id for all matching records


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
    
        $upsData = UpsData::query()
            ->where('app_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();
    
        if (!$upsData) {
            return response()->json(['error' => 'No data found for the specified user.'], 404);
        }
    
        // Extract relevant parameters
        $batteryVoltage = $upsData->battery_voltage;
        $inputVoltage = $upsData->input_voltage;
        $outputCurrent = $upsData->output_current;
        $utilityFail = $upsData->utility_fail;
        $batteryLow = $upsData->battery_low;
        $percentage = $upsData->percentage ?? $this->calculatePercentage($batteryVoltage);
    
        // Determine UPS status
        if (!$utilityFail && $outputCurrent > 0) {
            $status = 'Charging'; // Grid power is active and charging the battery
        } elseif ($utilityFail && $outputCurrent > 0) {
            $status = 'Discharging'; // Grid power failed, UPS is using battery
        } elseif (!$utilityFail && $outputCurrent == 0 && $percentage >= 100) {
            $status = 'Standby'; // Grid power is active, no load, battery fully charged
        } else {
            $status = 'Unknown';
        }
    
        // Calculate SOC
        if ($status === 'Charging') {
            $soc = 0.4631 * $batteryVoltage - 5.468;
        } else {
            if ($percentage <= 0) {
                return response()->json(['error' => 'Percentage required for discharging SOC calculation.'], 400);
            }
            $soc = (0.4631 * pow($batteryVoltage, 2) - 5.1578 * $batteryVoltage + 34.737 * $percentage) /
                ($batteryVoltage + 0.25474 * $percentage);
        }
    
        // Clamp SOC
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
            'charging' => $status === 'Charging',
            'output_current' => $outputCurrent,
            'status' => $status, // UPS status
        ]);
    }
    
    private function calculatePercentage($voltage)
    {
        $minVoltage = 11.0; // 0% charge
        $maxVoltage = 13.5; // 100% charge
    
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

    public function userSpecificationsIndex(Request $request)
    {
        // Retrieve the current authenticated user's ID
        $userId = auth()->id();

        // Query the UpsSpecification model and filter by the authenticated user
        $query = UpsSpecification::where('app_user_id', $userId);

        if ($request->has('unique_id')) {
            $query->where('unique_id', $request->unique_id);
        }

        // Get the filtered results
        $upsSpecifications = $query->first();

        // Check if data exists
        if (!$upsSpecifications) {
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




    public function devicechargingStoreBk(Request $request)
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
        $query = DeviceCharging::query()->where('app_user_id', auth()->id());
    
        // Check for serial_key parameter
        if ($request->has('serial_key')) {
            $query->where('serial_key', $request->serial_key);
        }
    
        // Check for specific_day parameter
        if ($request->has('specific_day')) {
            $query->where('specific_day', $request->specific_day);
        }
    
        // Handle time_range filters
        if ($request->has('time_range')) {
            $timeRange = $request->time_range;
    
            switch ($timeRange) {
                case '1D': $query->whereDate('created_at', '>=', now()->subDay()); break;
                case '5D': $query->whereDate('created_at', '>=', now()->subDays(5)); break;
                case '1M': $query->whereDate('created_at', '>=', now()->subMonth()); break;
                case '3M': $query->whereDate('created_at', '>=', now()->subMonths(3)); break;
                case '6M': $query->whereDate('created_at', '>=', now()->subMonths(6)); break;
                case '9M': $query->whereDate('created_at', '>=', now()->subMonths(9)); break;
                case '1Y': $query->whereDate('created_at', '>=', now()->subYear()); break;
                case '5Y': $query->whereDate('created_at', '>=', now()->subYears(5)); break;
                case 'Max': break; // No filter
                default:
                    return response()->json([
                        'status' => 400,
                        'message' => 'Invalid time range selected.',
                    ]);
            }
        }
    
        // Fetch the charging data with upsData relationship
        $chargingData = $query->with('upsData')->get();
    
        if ($chargingData->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No charging history found for the given criteria.',
                'graph_data' => [],
            ]);
        }
    
        // Prepare the response data dynamically
        $graphData = $chargingData->map(function ($item, $key) {
            $start = strtotime($item->charging_start_time);
            $end = strtotime($item->charging_end_time);
            $duration = gmdate('H:i:s', $end - $start);
    
            return [
                'id' => $key + 1,
                'time_slot' => date('hA', $start),
                'serial_key' => $item->serial_key,
                'specific_day' => $item->specific_day,
                'charging_start_time' => $item->charging_start_time,
                'charging_end_time' => $item->charging_end_time,
                'charging_status' => $item->charging_status,
                'event' => $item->event,
                'charging_duration' => $duration,
                'average_battery_voltage' => optional($item->UpsData)->battery_voltage ?? 0,
                'average_output_voltage' => optional($item->UpsData)->output_voltage ?? 0,
                'event_status' => match ($item->event) {
                    'Charging' => 1,
                    'Discharging' => 2,
                    'Standby' => 3,
                    default => 0,
                },
            ];
        });
    
        return response()->json([
            'status' => 200,
            'message' => 'Charging history retrieved successfully.',
            'graph_data' => $graphData,
        ]);
    }
    
    
    
    
    


    


    public function deviceChargingStore(Request $request)
    {
        $request->validate([
            'serial_key' => 'required|string',
            'charging_start_time' => 'required|date_format:Y-m-d H:i:s',
            'charging_end_time' => 'required|date_format:Y-m-d H:i:s',
            'charging_status' => 'required|string',
            'event' => 'required|string',
            'specific_day' => 'required|date_format:Y-m-d',
        ]);

        // Create a new record for each activity
        $chargingData = DeviceCharging::create([
            'serial_key' => $request->serial_key,
            'charging_start_time' => $request->charging_start_time,
            'charging_end_time' => $request->charging_end_time,
            'charging_status' => $request->charging_status,
            'event' => $request->event,
            'specific_day' => $request->specific_day,
        ]);

        // Return success response
        return response()->json([
            'status' => 201,
            'message' => 'Charging activity recorded successfully.',
            'data' => $chargingData,
        ]);
    }

}
