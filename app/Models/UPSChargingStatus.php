<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UPSChargingStatus extends Model
{
    // Table name (optional if it follows naming convention)
    protected $table = 'ups_charging_statuses';

    // Mass assignable fields
    protected $fillable = [
        'serial_key',
        'charging_start_time',
        'charging_end_time',
        'charging_status',
        'event',
        'specific_day',
    ];

    // Optional: Casts for datetime and date fields
    protected $casts = [
        'charging_start_time' => 'datetime',
        'charging_end_time' => 'datetime',
        'specific_day' => 'date',
    ];
}
