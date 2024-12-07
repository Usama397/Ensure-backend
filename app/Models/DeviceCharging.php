<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceCharging extends Model
{
    use HasFactory;
    protected $table = 'device_charging'; // Explicit table name

    protected $fillable = [
        'serial_key',
        'charging_start_time',
        'charging_end_time',
        'charging_status',
        'event',
        'specific_day',
        'app_user_id',
        'user_id',
    ];
    public function appUser()
{
    return $this->belongsTo(User::class, 'app_user_id');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}


