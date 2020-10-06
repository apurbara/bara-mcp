<?php

namespace Participant\Domain\Model\Participant\Worksheet;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Consultant\ConsultantComment,
    DependencyModel\Firm\Team,
    Model\Participant\Worksheet
};
use Resources\ {
    DateTimeImmutableBuilder,
    Exception\RegularException,
    Uuid
};

class Comment implements AssetBelongsToTeamInterface
{

    /**
     *
     * @var Worksheet
     */
    protected $worksheet;
    
    /**
     *
     * @var Comment
     */
    protected $parent;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $submitTime;

    /**
     *
     * @var bool
     */
    protected $removed;
    
    /**
     *
     * @var ConsultantComment||null
     */
    protected $consultantComment;
    
    /**
     *
     * @var ArrayCollection
     */
    protected $commentActivityLogs;

    public function __construct(Worksheet $worksheet, string $id, string $message, ?TeamMembership $teamMember)
    {
        $this->worksheet = $worksheet;
        $this->parent = null;
        $this->id = $id;
        $this->message = $message;
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->removed = false;
        
        $this->commentActivityLogs = new ArrayCollection();
        $this->addActivityLog("submitted comment", $teamMember);
    }
    
    public function createReply(string $id, string $message, ?TeamMembership $teamMember): self
    {
        $reply = new static($this->worksheet, $id, $message, $teamMember);
        $reply->parent = $this;
        return $reply;
    }

    public function remove(?TeamMembership $teamMember): void
    {
        if ($this->isConsultantComment()) {
            $errorDetail = 'forbidden: unable to remove consultant comment';
            throw RegularException::forbidden($errorDetail);
        }
        $this->removed = true;
        $this->addActivityLog("removed comment", $teamMember);
    }
    
    public function getWorksheetId(): string
    {
        return $this->worksheet->getId();
    }
    
    public function isConsultantComment(): bool
    {
        return !empty($this->consultantComment);
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->worksheet->belongsToTeam($team);
    }
    
    protected function addActivityLog(string $message, ?TeamMembership $teamMember): void
    {
        $messageWithActor = isset($teamMember)? "team member $message": "participant $message";
        $id = Uuid::generateUuid4();
        $commentActivityLog = new Comment\CommentActivityLog($this, $id, $messageWithActor);
        if (isset($teamMember)) {
            $teamMember->setAsActivityOperator($commentActivityLog);
        }
        $this->commentActivityLogs->add($commentActivityLog);
    }

}
