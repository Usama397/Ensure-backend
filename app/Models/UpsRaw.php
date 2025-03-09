<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsRaw extends Model
{
    use HasFactory;

    protected $table = 'ups_raw';

    protected $fillable = [
        'raw_data'
    ];
}
