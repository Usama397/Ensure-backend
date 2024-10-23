<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwilioSetting extends Model
{
    use HasFactory;

    protected $table = 'twilio_settings';

    protected $fillable = ['account_sid', 'auth_token', 'number', 'updated_by'];
}
