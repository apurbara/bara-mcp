<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\{
    Event\ConsultantCommentOnWorksheetEvent,
    Model\Firm\Personnel\ProgramConsultant,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Program\Participant\Worksheet\Comment
};
use Resources\Domain\Model\ModelContainEvents;

class ConsultantComment extends ModelContainEvents
{

    /**
     *
     * @var ProgramConsultant
     */
    protected $programConsultant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Comment
     */
    protected $comment;

    function __construct(ProgramConsultant $programConsultant, string $id, Comment $comment)
    {
        $this->programConsultant = $programConsultant;
        $this->id = $id;
        $this->comment = $comment;

        $firmId = $this->programConsultant->getPersonnel()->getFirm()->getId();
        $personnelId = $this->programConsultant->getPersonnel()->getId();
        $consultantId = $this->programConsultant->getId();
        $messageForParticipant = "consultant {$this->programConsultant->getPersonnel()->getName()} has commented on your worksheet";
        
        $event = new ConsultantCommentOnWorksheetEvent(
                $firmId, $personnelId, $consultantId, $this->id, $messageForParticipant);
        $this->recordEvent($event);
    }

    public function remove(): void
    {
        $this->comment->remove();
    }

}