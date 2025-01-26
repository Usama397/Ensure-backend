<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DeviceCharging;

class DeviceChargingMonitorController extends Controller
{
    public function monitorNewEntries()
    {
        // Infinite loop to continuously monitor the table
        while (true) {
            // Fetch new records with NULL app_user_id
            $newRecords = DeviceCharging::whereNull('app_user_id')->get();

            foreach ($newRecords as $record) {
                // Check if an existing record with the same unique_id and non-NULL app_user_id exists
                $existingRecord = DeviceCharging::where('serial_key', $record->serial_key)
                    ->whereNotNull('app_user_id')
                    ->first();

                if ($existingRecord) {
                    // Set the app_user_id from the existing record
                    $record->app_user_id = $existingRecord->app_user_id;
                    $record->save();

                    Log::info("Updated app_user_id for record ID {$record->id} with unique_id: {$record->serial_key}");
                }
            }

            // Sleep for 5 seconds before checking again
            sleep(5);
        }
    }
}
