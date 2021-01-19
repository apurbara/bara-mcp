<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfConsultationSession,
    Record,
    Shared\RecordOfFormRecord
};

class RecordOfConsultantFeedback implements Record
{

    /**
     *
     * @var RecordOfConsultationSession
     */
    public $consultationSession;

    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $id;
    public $participantRating;

    function __construct(RecordOfConsultationSession $consultationSession, RecordOfFormRecord $formRecord)
    {
        $this->consultationSession = $consultationSession;
        $this->formRecord = $formRecord;
        $this->id = $formRecord->id;
        $this->participantRating = 2;
    }

    public function toArrayForDbEntry()
    {
        return [
            "ConsultationSession_id" => $this->consultationSession->id,
            "FormRecord_id" => $this->formRecord->id,
            "id" => $this->id,
            "participantRating" => $this->participantRating,
        ];
    }

}
