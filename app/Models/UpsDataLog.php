<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsDataLog extends Model
{
    use HasFactory;

    protected $fillable = ['ups_data_id', 'user_id', 'data', 'action'];

    protected $casts = [
        'data' => 'array', // Store JSON data as an array
    ];

    public function upsData()
    {
        return $this->belongsTo(UpsData::class);
    }
}
