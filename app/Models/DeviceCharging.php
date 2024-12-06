<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceCharging extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_key',
        'charging_start_time',
        'charging_end_time',
        'charging_status',
        'event',
        'specific_day',
    ];
}

