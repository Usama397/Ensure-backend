<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    use HasFactory;

    protected $fillable = ['ups_id', 'event_type', 'timestamp', 'duration'];

    public function upsData()
    {
        return $this->belongsTo(UpsData::class, 'ups_id');
    }
}
