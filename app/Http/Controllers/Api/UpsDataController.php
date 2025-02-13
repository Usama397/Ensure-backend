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
use App\Models\UpsDataLog;
use App\Services\GoogleSheetsService;
use App\Jobs\AppendToGoogleSheetsJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log; // Ensure this is at the top

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
        // Log the incoming request
        Log::info('Incoming API Request:', ['data' => $request->all()]);
    
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
            'charging_status' => 'required|boolean',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 400);
        }
    
        $validatedData = $validator->validated();
        $cacheKey = "ups_data_{$request->unique_id}_{$request->user_id}";
        $cachedData = Cache::get($cacheKey);
    
        if ($cachedData && $cachedData == $validatedData) {
            Log::info('Skipping update: No change in data.');
            return response()->json([
                'status' => 200,
                'message' => 'No changes detected, skipping update',
            ]);
        }
    
        Cache::put($cacheKey, $validatedData, 10);
    
        // Check if record exists
        $upsData = UpsData::where('unique_id', $request->unique_id)
            ->where('user_id', $request->user_id)
            ->first();
    
        if ($upsData) {
            // Log before updating
            Log::info('Updating existing UPS Data:', ['old_data' => $upsData->toArray(), 'new_data' => $validatedData]);
    
            $upsData->update($validatedData);
            $action = 'updated';
        } else {
            Log::info('Creating new UPS Data:', ['data' => $validatedData]);
    
            $upsData = UpsData::create($validatedData);
            $action = 'created';
        }
    
        // Log the action
        UpsDataLog::create([
            'ups_data_id' => $upsData->id,
            'user_id' => $request->user_id,
            'data' => json_encode($validatedData),
            'action' => $action,
        ]);
    
        // Ensure that updates are logged in Google Sheets
        Log::info('Dispatching Google Sheets Job', ['data' => $validatedData, 'action' => $action]);
    
        AppendToGoogleSheetsJob::dispatch([
            now()->toDateTimeString(),
            $request->unique_id,
            $request->user_id,
            $request->input_voltage,
            $request->input_fault_voltage,
            $request->output_voltage,
            $request->output_current,
            $request->output_frequency,
            $request->battery_voltage,
            $request->temperature,
            $request->utility_fail,
            $request->battery_low,
            $request->avr_normal,
            $request->ups_failed,
            $request->ups_line_interactive,
            $request->test_in_progress,
            $request->shutdown_active,
            $request->beeper_on,
            $request->charging_status,
            $action
        ])->delay(now()->addSeconds(10));
    
        return response()->json([
            'status' => $upsData->wasRecentlyCreated ? 201 : 200,
            'message' => 'UPS data ' . $action . ' successfully',
            'data' => $upsData,
        ]);
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
            return response()->json(['error' => 'No UPS data found for this user.'], 404);
        }
    
        // Retrieve necessary values
        $batteryVoltage = $upsData->battery_voltage;
        $outputCurrent = $upsData->output_current;
        $chargingStatus = $upsData->charging_status; // 1 = Charging, 0 = Not Charging
    
        // Calculate percentage based on battery voltage
        $percentage = $this->calculatePercentage($batteryVoltage);
    
        // Determine UPS Mode
        if ($chargingStatus == 1) {
            $mode = 'Charging';
        } elseif ($chargingStatus == 0 && $outputCurrent > 0) {
            $mode = 'Discharging';
        } else {
            $mode = 'Standby';
        }
    
        // Calculate SOC based on mode
        if ($mode == 'Charging') {
            $soc = 0.4631 * $batteryVoltage - 5.468;
        } elseif ($mode == 'Discharging') {
            if ($percentage <= 0) {
                return response()->json(['error' => 'Percentage required for Discharging SOC calculation.'], 400);
            }
            $soc = (0.4631 * pow($batteryVoltage, 2) - 5.1578 * $batteryVoltage + 34.737 * $percentage) /
                ($batteryVoltage + 0.25474 * $percentage);
        } else { // Standby Mode
            $soc = $this->calculateStandbySOC($batteryVoltage);
        }
    
        // Ensure SOC is within range 0-1
        $soc = max(0, min(1, $soc));
    
        return response()->json([
            'status' => $mode,
            'soc' => round($soc, 3),
            'battery_voltage' => $batteryVoltage,
            'charging' => $chargingStatus,
            'output_current' => $outputCurrent,
            'percentage' => round($percentage, 2) . ' %',
        ]);
    }
    

    private function calculateStandbySOC($batteryVoltage)
    {
        // Placeholder formula - Client might provide a more refined equation
        return max(0, min(1, 0.8 * ($batteryVoltage / 13.0))); // Normalizing to a 0-1 scale
    }   
    
    private function calculatePercentage($voltage)
    {
        $minVoltage = 11.0;  // Represents 0% charge
        $maxVoltage = 13.5;  // Represents 100% charge
    
        // Ensure voltage is within bounds
        if ($voltage <= $minVoltage) {
            return 0;
        }
        if ($voltage >= $maxVoltage) {
            return 100;
        }
    
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
