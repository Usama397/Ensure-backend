<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsRaw extends Model
{
    use HasFactory;

    protected $table = 'ups_raw';

    protected $fillable = [
        'unique_id', // Optional, if extracting unique_id
        'raw_data'
    ];
}
