<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UpsData;
use Illuminate\Http\Request;
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
        $upsData = UpsData::find($id);

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

        $upsData = UpsData::where('user_id', $request->user_id)->first();

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
}
