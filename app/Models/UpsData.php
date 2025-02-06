<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsData extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'user_id',
        'app_user_id',
        'input_voltage',
        'input_fault_voltage',
        'output_voltage',
        'output_current',
        'output_frequency',
        'battery_voltage',
        'temperature',
        'utility_fail',
        'battery_low',
        'avr_normal',
        'ups_failed',
        'ups_line_interactive',
        'test_in_progress',
        'shutdown_active',
        'beeper_on',
        'charging_status'
    ];

    protected $casts = [
        'charging_status' => 'boolean', // Ensure boolean casting
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function eventLogs()
    {
        return $this->hasMany(EventLog::class, 'ups_id');
    }
}
