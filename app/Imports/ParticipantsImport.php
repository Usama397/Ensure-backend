<?php

namespace App\Imports;

use App\Models\Participant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantsImport implements ToModel, WithHeadingRow
{
    private $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }

    public function model(array $row)
    {
        $existingParticipant = Participant::where([
            'event_id' => $this->eventId,
            'name' => trim($row['name']),
            'gender_id' => trim($row['gender_id']),
            'phone_number' => trim($row['phone_number']),
        ])->first();

        if ($existingParticipant) {
            $existingParticipant->update([
                'name' => trim($row['name']),
                'gender_id' => trim($row['gender_id']),
                'phone_number' => trim($row['phone_number']),
            ]);
            return $existingParticipant;
        }

        //Create new Participant
        return new Participant([
            'event_id' => $this->eventId,
            'name' => trim($row['name']),
            'gender_id' => trim($row['gender_id']),
            'phone_number' => trim($row['phone_number']),
        ]);
    }
}
