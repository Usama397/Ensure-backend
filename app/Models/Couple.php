<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couple extends Model
{
    use HasFactory;

    protected $table = 'couples';

    protected $fillable = [
        'event_id',
        'female_participant_id', 
        'male_participant_id', 
        'status',
        'female_dress_message_sent',
        'female_dress_reply',
        'female_dress_message_exchanged',
        'female_match_reply',
        'male_dress_message_sent',
        'male_dress_reply',
        'male_dress_message_exchanged',
        'male_match_reply',
        'numbers_exchanged',];

    public function femaleParticipant()
    {
        return $this->belongsTo(Participant::class, 'female_participant_id');
    }

    public function maleParticipant()
    {
        return $this->belongsTo(Participant::class, 'male_participant_id');
    }
}
