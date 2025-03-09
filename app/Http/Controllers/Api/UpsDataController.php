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
        // Log the raw incoming request for debugging
        // Log::info('Raw Incoming API Request:', ['body' => $request->getContent()]);
    
        // Validate and normalize input data
        $validatedData = [
            'unique_id' => $request->input('unique_id', 'ESP_UNKNOWN'),
            'user_id' => $request->input('user_id', 0), // Default to 0 if missing
            'input_voltage' => (float) $request->input('input_voltage', 0.0),
            'input_fault_voltage' => (float) $request->input('input_fault_voltage', 0.0),
            'output_voltage' => (float) $request->input('output_voltage', 0.0),
            'output_current' => (int) $request->input('output_current', 0),
            'output_frequency' => (float) $request->input('output_frequency', 50.0),
            'battery_voltage' => (float) $request->input('battery_voltage', 0.0),
            'temperature' => (float) $request->input('temperature', 0.0),
            'utility_fail' => filter_var($request->input('utility_fail', false), FILTER_VALIDATE_BOOLEAN),
            'battery_low' => filter_var($request->input('battery_low', false), FILTER_VALIDATE_BOOLEAN),
            'avr_normal' => filter_var($request->input('avr_normal', true), FILTER_VALIDATE_BOOLEAN),
            'ups_failed' => filter_var($request->input('ups_failed', false), FILTER_VALIDATE_BOOLEAN),
            'ups_line_interactive' => filter_var($request->input('ups_line_interactive', false), FILTER_VALIDATE_BOOLEAN),
            'test_in_progress' => filter_var($request->input('test_in_progress', false), FILTER_VALIDATE_BOOLEAN),
            'shutdown_active' => filter_var($request->input('shutdown_active', false), FILTER_VALIDATE_BOOLEAN),
            'beeper_on' => filter_var($request->input('beeper_on', false), FILTER_VALIDATE_BOOLEAN),
            'charging_status' => filter_var($request->input('charging_status', false), FILTER_VALIDATE_BOOLEAN),
        ];
    
        // Cache Key for UPS (prevent redundant writes)
        $cacheKey = "ups_data_{$validatedData['unique_id']}";
        $cachedData = Cache::get($cacheKey);
    
        // Skip update if data has not changed
        if ($cachedData && $cachedData == $validatedData) {
            // Log::info('Skipping update: No change in data.');
            return response()->json([
                'status' => 200,
                'message' => 'No changes detected, skipping update',
            ]);
        }
    
        // Store in cache for 10 seconds to prevent redundant updates
        Cache::put($cacheKey, $validatedData, 10);
    
        // Check if a record already exists (ignore `user_id`)
        $upsData = UpsData::where('unique_id', $validatedData['unique_id'])->first();
    
        if ($upsData) {
            // Log::info('Updating existing UPS Data:', ['old_data' => $upsData->toArray(), 'new_data' => $validatedData]);
            $upsData->update($validatedData);
            $action = 'updated';
        } else {
            // Log::info('Creating new UPS Data:', ['data' => $validatedData]);
            $upsData = UpsData::create($validatedData);
            $action = 'created';
        }
    
        // Try inserting log entry, but don't let failure affect Google Sheets
        try {
            UpsDataLog::create([
                'ups_data_id' => $upsData->id,
                'user_id' => $validatedData['user_id'], // Use `user_id` if available
                'data' => json_encode($validatedData),
                'action' => $action,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to insert into UpsDataLog: " . $e->getMessage());
        }
    
        // Ensure Google Sheets job always runs
        try {
            AppendToGoogleSheetsJob::dispatch([
                now()->toDateTimeString(),
                $validatedData['unique_id'],
                $validatedData['input_voltage'],
                $validatedData['input_fault_voltage'],
                $validatedData['output_voltage'],
                $validatedData['output_current'],
                $validatedData['output_frequency'],
                $validatedData['battery_voltage'],
                $validatedData['temperature'],
                $validatedData['utility_fail'],
                $validatedData['battery_low'],
                $validatedData['avr_normal'],
                $validatedData['ups_failed'],
                $validatedData['ups_line_interactive'],
                $validatedData['test_in_progress'],
                $validatedData['shutdown_active'],
                $validatedData['beeper_on'],
                $validatedData['charging_status'],
                $action
            ])->delay(now()->addSeconds(10));
    
            // Log::info('Google Sheets Job Dispatched', ['data' => $validatedData]);
        } catch (\Exception $e) {
            Log::error("Google Sheets Dispatch Error: " . $e->getMessage());
        }
    
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
        $b7 = $upsData->utility_fail;
        $chargingInquiry = $upsData->charging_status;
        $qqq = $upsData->output_current; // Assuming QQQ represents output current
    
        // Calculate SOC using the same formula for all modes
        $soc = $this->calculateSOC($batteryVoltage, $outputCurrent);
    
        // Ensure SOC is within range 0-100
        $socPercentage = max(0, min(100, $soc * 100));
    
        // Determine UPS Mode based strictly on the given conditions
        if ($chargingInquiry == 1 && $b7 == 0) {
            $mode = 'Charging';
        } elseif ($b7 == 1 && $chargingInquiry == 0) {
            $mode = 'Discharging';
        } elseif ($chargingInquiry == 0 && $b7 == 0) {
            $mode = 'Standby';
        }
    
        return response()->json([
            'status' => $mode,
            'soc' => round($socPercentage, 3),
            'battery_voltage' => $batteryVoltage,
            'charging' => $chargingInquiry,
            'output_current' => $outputCurrent,
            'percentage' => intval($socPercentage),
        ]);
    }
    
    private function calculateSOC($S, $Q)
    {
        return 1.03 / (1 + exp(-3.37 * ($S + ($Q * 0.598) / $S - 12.26)));
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
