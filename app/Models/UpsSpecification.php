<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_user_id',
        'unique_id',
        'continuous_power',
        'energy',
        'dimensions'
    ];
}
